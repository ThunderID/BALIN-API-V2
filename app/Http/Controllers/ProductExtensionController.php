<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of Product Extension
 * 
 * @author cmooy
 */
class ProductExtensionController extends Controller
{
	/**
	 * Display all Extensions
	 *
	 * @param search, skip, take
	 * @return Response
	 */
	public function index()
	{
		$result                 = new \App\Models\ProductExtension;

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

		$result                     = $result->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display a Extension
	 *
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Models\ProductExtension::id($id)->with(['images'])->first();
	   
		if($result)
		{
			return new JSend('success', (array)$result->toArray());

		}
		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');

	}

	/**
	 * Store a Product Extension
	 *
	 * 1. Save Extension
	 * 2. Save Image
	 * 
	 * @return Response
	 */
	public function store()
	{
		if(!Input::has('extension'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data extension.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. Validate Extension Parameter
		$extension					= Input::get('extension');
		
		if(is_null($extension['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$extension_rules			=   [
											'name'						=> 'required|max:255',
											'price'						=> 'numeric',
											'is_active'					=> 'boolean',
											'is_customize'				=> 'boolean',
										];

		//1a. Get original data
		$extension_data				= \App\Models\ProductExtension::findornew($extension['id']);

		//1b. Validate Basic Extension Parameter
		$validator                  = Validator::make($extension, $extension_rules);

		if (!$validator->passes())
		{
			$errors->add('Extension', $validator->errors());
		}
		else
		{
			//if validator passed, save Extension
			$extension_data           = $extension_data->fill($extension);

			if(!$extension_data->save())
			{
				$errors->add('Extension', $extension_data->getError());
			}
		}
		//End of validate Extension

		//2. Validate Extension Image Parameter
		if(!$errors->count() && isset($extension['images']) && is_array($extension['images']))
		{
			$image_current_ids		= [];
			foreach ($extension['images'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$image_data		= \App\Models\Image::findornew($value['id']);

					$image_rules	=   [
											'imageable_id'              => 'exists:Extensions,id|'.($is_new ? '' : 'in:'.$extension_data['id']),
											'imageable_type'			=> ($is_new ? '' : 'in:'.get_class($extension_data)),
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
						$value['imageable_id']          = $extension_data['id'];
						$value['imageable_type']        = get_class($extension_data);

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
				$images                            = \App\Models\Image::imageableid($extension['id'])->imageabletype(get_class($extension_data))->get(['id'])->toArray();
				
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
		//End of validate Extension image

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_extension                 = \App\Models\ProductExtension::id($extension_data['id'])->with(['images'])->first()->toArray();

		return new JSend('success', (array)$final_extension);
	}

	/**
	 * Delete a Extension
	 *
	 * @return Response
	 */
	public function delete($id = null)
	{
		//
		$extension                    = \App\Models\ProductExtension::id($id)->with(['images'])->first();

		if(!$extension)
		{
			return new JSend('error', (array)Input::all(), 'Extension tidak ditemukan.');
		}

		$result                     = $extension->toArray();

		if($extension->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $extension->getError());
	}
}
