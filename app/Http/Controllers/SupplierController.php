<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of Supplier
 * 
 * @author cmooy
 */
class SupplierController extends Controller
{
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

		$result                     = $result->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
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
			return new JSend('success', (array)$result->toArray());

		}
		return response()->json( JSend::fail(['ID Tidak Valid.']));

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
			return new JSend('error', (array)Input::all(), 'Tidak ada data supplier.');
		}

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Validate Supplier Parameter
		$supplier                    = Input::get('supplier');
		if(is_null($supplier['id']))
		{
			$is_new                 = true;
		}
		else
		{
			$is_new                 = false;
		}

		$supplier_rules             =   [
											'name'                      => 'required|max:255',
										];

		//1a. Get original data
		$supplier_data              = \App\Entities\Supplier::findornew($supplier['id']);

		//1b. Validate Basic Supplier Parameter
		$validator                  = Validator::make($supplier, $supplier_rules);

		if (!$validator->passes())
		{
			$errors->add('Supplier', $validator->errors());
		}
		else
		{
			//if validator passed, save supplier
			$supplier_data           = $supplier_data->fill($supplier);

			if(!$supplier_data->save())
			{
				$errors->add('Supplier', $supplier_data->getError());
			}
		}
		//End of validate supplier

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_supplier              = \App\Entities\Supplier::id($supplier_data['id'])->first()->toArray();

		return new JSend('success', (array)$final_supplier);
	}

	/**
	 * Delete a supplier
	 *
	 * @return Response
	 */
	public function delete($id = null)
	{
		//
		$supplier                   = \App\Entities\Supplier::id($id)->first();

		if(!$supplier)
		{
			return new JSend('error', (array)Input::all(), 'Supplier tidak ditemukan.');
		}

		$result                     = $supplier->toArray();

		if($supplier->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $supplier->getError());
	}
}
