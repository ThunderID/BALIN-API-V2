<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of Courier
 * 
 * @author cmooy
 */
class CourierController extends Controller
{
	/**
	 * Display all couriers
	 *
	 * @param search, skip, take
	 * @return Response
	 */
	public function index()
	{
		$result                 = new \App\Models\Courier;

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'name':
						$result     = $result->name($value);
						break;
					
					default:
						# code...
						break;
				}
			}
		}

		if(Input::has('sort'))
		{
			$sort                 = Input::get('sort');

			foreach ($sort as $key => $value) 
			{
				if(!in_array($value, ['asc', 'desc']))
				{
					return new JSend('error', (array)Input::all(), $key.' harus bernilai asc atau desc.');
				}
				switch (strtolower($key)) 
				{
					case 'name':
						$result     = $result->orderby($key, $value);
						break;
					default:
						# code...
						break;
				}
			}
		}
		
		$count                      = $result->count();

		if(Input::has('skip'))
		{
			$skip                   = Input::get('skip');
			$result                 = $result->skip($skip);
		}

		if(Input::has('take'))
		{
			$take                   = Input::get('take');
			$result                 = $result->take($take);
		}

		$result                     = $result->with(['shippingcosts', 'addresses'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display a courier
	 *
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Models\Courier::id($id)->with(['shippingcosts', 'addresses', 'images', 'shippings', 'shippings.address', 'shippings.sale'])->first();
	   
		if($result)
		{
			return new JSend('success', (array)$result->toArray());

		}
		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');

	}

	/**
	 * Store a courier
	 *
	 * 1. Save Courier
	 * 2. Save Shipping Cost
	 * 3. Save Address
	 * 4. Save Image
	 * 
	 * @return Response
	 */
	public function store()
	{
		if(!Input::has('courier'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data courier.');
		}

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Validate Courier Parameter
		$courier                    = Input::get('courier');
		
		if(is_null($courier['id']))
		{
			$is_new                 = true;
		}
		else
		{
			$is_new                 = false;
		}

		$courier_rules             =   [
											'name'                      => 'required|max:255',
										];

		//1a. Get original data
		$courier_data              = \App\Models\Courier::findornew($courier['id']);

		//1b. Validate Basic Courier Parameter
		$validator                  = Validator::make($courier, $courier_rules);

		if (!$validator->passes())
		{
			$errors->add('Courier', $validator->errors());
		}
		else
		{
			//if validator passed, save courier
			$courier_data           = $courier_data->fill($courier);

			if(!$courier_data->save())
			{
				$errors->add('Courier', $courier_data->getError());
			}
		}
		//End of validate courier

		//2. Validate Shipping Cost Parameter
		if(!$errors->count() && isset($courier['shippingcosts']) && is_array($courier['shippingcosts']))
		{
			$cost_current_ids         = [];
			foreach ($courier['shippingcosts'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$cost_data		= \App\Models\ShippingCost::findornew($value['id']);

					$cost_rules		=	[
											'courier_id'			=> 'exists:couriers,id|'.($is_new ? '' : 'in:'.$courier_data['courier_id']),
											'start_postal_code'		=> 'required|max:255|',
											'end_postal_code'		=> 'required|max:255',
											'started_at'			=> 'required|date_format:"Y-m-d H:i:s"',
											'cost'					=> 'required|numeric|',
										];

					$validator      = Validator::make($value, $cost_rules);

					//if there was cost and validator false
					if (!$validator->passes())
					{
						$errors->add('Cost', $validator->errors());
					}
					else
					{
						$value['courier_id']        = $courier_data['id'];

						$cost_data                    = $cost_data->fill($value);

						if(!$cost_data->save())
						{
							$errors->add('Cost', $cost_data->getError());
						}
						else
						{
							$cost_current_ids[]       = $cost_data['id'];
						}
					}
				}
			}
			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$costs                            = \App\Models\ShippingCost::courierid($courier['id'])->get(['id'])->toArray();
				
				$cost_should_be_ids               = [];
				foreach ($costs as $key => $value) 
				{
					$cost_should_be_ids[]         = $value['id'];
				}

				$difference_cost_ids              = array_diff($cost_should_be_ids, $cost_current_ids);

				if($difference_cost_ids)
				{
					foreach ($difference_cost_ids as $key => $value) 
					{
						$cost_data                = \App\Models\ShippingCost::find($value);

						if(!$cost_data->delete())
						{
							$errors->add('Cost', $cost_data->getError());
						}
					}
				}
			}
		}

		//3. Validate courier address Parameter
		if(!$errors->count() && isset($courier['addresses']) && is_array($courier['addresses']))
		{
			$address_current_ids         = [];
			foreach ($courier['addresses'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$address_data		= \App\Models\Address::findornew($value['id']);

					$address_rules		=   [
												'owner_id'		=> 'exists:couriers,id|'.($is_new ? '' : 'in:'.$courier_data['id']),
												'owner_type'	=> ($is_new ? '' : 'in:'.get_class($courier_data)),
												'phone'			=> 'required|max:255',
												'address'		=> 'required',
												'zipcode'		=> 'required|max:255',
											];

					$validator      	= Validator::make($value, $address_rules);

					//if there was address and validator false
					if (!$validator->passes())
					{
						$errors->add('Address', $validator->errors);
					}
					else
					{
						$value['owner_id']                  = $courier_data['id'];
						$value['owner_type']                = get_class($courier_data);

						$address_data                       = $address_data->fill($value);

						if(!$address_data->save())
						{
							$errors->add('Address', $address_data->getError());
						}
						else
						{
							$address_current_ids[]          = $address_data['id'];
						}
					}
				}
			}
			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$addresses                            = \App\Models\Address::ownerid($courier['id'])->ownertype(get_class($courier_data))->get(['id'])->toArray();
				
				$address_should_be_ids               = [];
				foreach ($addresses as $key => $value) 
				{
					$address_should_be_ids[]         = $value['id'];
				}

				$difference_address_ids              = array_diff($address_should_be_ids, $address_current_ids);

				if($difference_address_ids)
				{
					foreach ($difference_address_ids as $key => $value) 
					{
						$address_data                = \App\Models\Address::find($value);

						if(!$address_data->delete())
						{
							$errors->add('Address', $address_data->getError());
						}
					}
				}
			}
		}
		//End of validate courier image

		//4. Validate courier Image Parameter
		if(!$errors->count() && isset($courier['images']) && is_array($courier['images']))
		{
			$image_current_ids		= [];
			foreach ($courier['images'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$image_data		= \App\Models\Image::findornew($value['id']);

					$image_rules	=   [
											'imageable_id'              => 'exists:couriers,id|'.($is_new ? '' : 'in:'.$courier_data['id']),
											'imageable_type'			=> ($is_new ? '' : 'in:'.get_class($courier_data)),
											'thumbnail'                 => 'required|max:255',
											'image_xs'                  => 'required|max:255',
											'image_sm'                  => 'required|max:255',
											'image_md'                  => 'required|max:255',
											'image_lg'                  => 'required|max:255',
											'is_default'                => 'boolean',
										];

					$validator      	= Validator::make($value, $image_rules);

					//if there was image and validator false
					if (!$validator->passes())
					{
						$errors->add('Image', $validator->errors());
					}
					else
					{
						$value['imageable_id']          = $courier_data['id'];
						$value['imageable_type']        = get_class($courier_data);

						$image_data                     = $image_data->fill($value);

						if(!$image_data->save())
						{
							$errors->add('Image', $image_data->getError());
						}
						else
						{
							$image_current_ids[]       = $image_data['id'];
						}
					}
				}
			}
			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$images                            = \App\Models\Image::imageableid($courier['id'])->imageabletype(get_class($courier_data))->get(['id'])->toArray();
				
				$image_should_be_ids               = [];
				foreach ($images as $key => $value) 
				{
					$image_should_be_ids[]         = $value['id'];
				}

				$difference_image_ids              = array_diff($image_should_be_ids, $image_current_ids);

				if($difference_image_ids)
				{
					foreach ($difference_image_ids as $key => $value) 
					{
						$image_data                = \App\Models\Image::find($value);

						if(!$image_data->delete())
						{
							$errors->add('Image', $image_data->getError());
						}
					}
				}
			}
		}
		//End of validate courier image

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_courier                 = \App\Models\Courier::id($courier_data['id'])->with(['shippingcosts', 'addresses', 'images'])->first()->toArray();

		return new JSend('success', (array)$final_courier);
	}

	/**
	 * Delete a courier
	 *
	 * @return Response
	 */
	public function delete($id = null)
	{
		//
		$courier                    = \App\Models\Courier::id($id)->with(['shippingcosts'])->first();

		if(!$courier)
		{
			return new JSend('error', (array)Input::all(), 'Kurir tidak ditemukan.');
		}

		$result                     = $courier->toArray();

		if($courier->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $courier->getError());
	}
}
