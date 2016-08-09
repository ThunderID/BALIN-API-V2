<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Services\BalinStoreOrnament;
use App\Services\BalinDeleteOrnament;

/**
 * Handle Protected Resource of Product Extension
 * 
 * @author cmooy
 */
class ProductExtensionController extends Controller
{
	public function __construct(Request $request, BalinStoreOrnament $store_ornament, BalinDeleteOrnament $delete_ornament)
	{
		$this->request 				= $request;
		$this->store_ornament		= $store_ornament;
		$this->delete_ornament		= $delete_ornament;
	}

	/**
	 * Display all Extensions
	 *
	 * @param search, skip, take
	 * @return Response
	 */
	public function index()
	{
		$result                 = new \App\Entities\ProductExtension;

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
		$result                 = \App\Entities\ProductExtension::id($id)->with(['images'])->first();
	   
		if($result)
		{
			return new JSend('success', (array)$result->toArray());

		}
		return response()->json( JSend::fail(['ID Tidak Valid.']));

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

		$ornament				= Input::get('extension');

		$this->store_ornament->fill($ornament);

		if(!$this->store_ornament->save())
		{
			return response()->json( JSend::error($this->store_ornament->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($this->store_ornament->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Delete a Extension
	 *
	 * @return Response
	 */
	public function delete($id = null)
	{
		//
		$ornament                    = \App\Entities\ProductExtension::id($id)->with(['images'])->first();

		if(!$ornament)
		{
			return response()->json( JSend::error(['Extension tidak ditemukan.']));
		}

		if($this->delete_ornament->delete($ornament))
		{
			return response()->json( JSend::success(['data' => $this->delete_ornament->getData()])->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::error($this->delete_ornament->getError())->asArray());
	}
}
