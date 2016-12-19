<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Services\BalinStoreExpedition;
use App\Services\BalinDeleteExpedition;
/**
 * Handle Protected Resource of Courier
 * 
 * @author cmooy
 */
class CourierController extends Controller
{
	public function __construct(Request $request, BalinStoreExpedition $store_courier, BalinDeleteExpedition $delete_courier)
	{
		$this->request 				= $request;
		$this->store_courier		= $store_courier;
		$this->delete_courier		= $delete_courier;
	}

	/**
	 * Display all couriers
	 *
	 * @param search, skip, take
	 * @return Response
	 */
	public function index()
	{
		$result                 = new \App\Entities\Courier;

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
					return response()->json( JSend::error([$key.' harus bernilai asc atau desc.'])->asArray());
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

		$result                     = $result->with(['shippingcosts', 'addresses'])->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Display a courier
	 *
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Entities\Courier::id($id)->with(['shippingcosts', 'addresses', 'images', 'shippings', 'shippings.address', 'shippings.sale'])->first();
	   
		if($result)
		{
			return response()->json( JSend::success($result->toArray())->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::error(['ID Tidak Valid.'])->asArray());

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
			return response()->json( JSend::error(['Tidak ada data courier.'])->asArray());
		}

		//1. Validate Courier Parameter
		$courier			= Input::get('courier');

		$this->store_courier->fill($courier);

		if(!$this->store_courier->save())
		{
			return response()->json( JSend::error($this->store_courier->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($this->store_courier->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Delete a courier
	 *
	 * @return Response
	 */
	public function delete($id = null)
	{
		//
		$courier					= \App\Entities\Courier::id($id)->with(['shippingcosts'])->first();

		if(!$courier)
		{
			return response()->json( JSend::error(['Kurir tidak ditemukan.'])->asArray());
		}

		if($this->delete_courier->delete($courier))
		{
			return response()->json( JSend::success($this->delete_courier->getData())->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::error($this->delete_courier->getError())->asArray());
	}
}
