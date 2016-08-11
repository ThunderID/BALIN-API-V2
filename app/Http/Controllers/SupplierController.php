<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Services\BalinStoreSupplier;
use App\Services\BalinDeleteSupplier;

/**
 * Handle Protected Resource of Supplier
 * 
 * @author cmooy
 */
class SupplierController extends Controller
{
	public function __construct(Request $request, BalinStoreSupplier $store_supplier, BalinDeleteSupplier $delete_supplier)
	{
		$this->request 				= $request;
		$this->store_supplier		= $store_supplier;
		$this->delete_supplier		= $delete_supplier;
	}
	
	/**
	 * Display all suppliers
	 *
	 * @param search, skip, take
	 * @return Response
	 */
	public function index()
	{
		$result                 = new \App\Entities\Supplier;

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

		$result						= $result->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Display a supplier
	 *
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Entities\Supplier::id($id)->first();
	
		if($result)
		{
			return response()->json( JSend::success($result->toArray())->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::error(['ID Tidak Valid.'])->asArray());
	}

	/**
	 * Store a supplier
	 *
	 * @return Response
	 */
	public function store()
	{
		if(!Input::has('supplier'))
		{
			return response()->json( JSend::error(['Tidak ada data supplier.'])->asArray());
		}

		//1. Validate supplier Parameter
		$supplier			= Input::get('supplier');

		$this->store_supplier->fill($supplier);

		if(!$this->store_supplier->save())
		{
			return response()->json( JSend::error($this->store_supplier->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($this->store_supplier->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Delete a supplier
	 *
	 * @return Response
	 */
	public function delete($id = null)
	{
		//
		$supplier					= \App\Entities\Supplier::id($id)->first();

		if(!$supplier)
		{
			return response()->json( JSend::error(['Supplier tidak ditemukan.'])->asArray());
		}

		if($this->delete_supplier->delete($supplier))
		{
			return response()->json( JSend::success($this->delete_supplier->getData())->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::error($this->delete_supplier->getError())->asArray());
	}
}
