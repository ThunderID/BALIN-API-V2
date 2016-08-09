<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Services\BalinStoreAdmin;

/**
 * Handle Protected Resource of admin
 * 
 * @author cmooy
 */
class AdminController extends Controller
{
	public function __construct(Request $request, BalinStoreAdmin $admin)
	{
		$this->request 				= $request;
		$this->admin 				= $admin;
	}

	/**
	 * Display all admins
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index()
	{
		$result                 = new \App\Entities\Admin;

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
					case 'role':
						$result     = $result->role($value);
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
		
		$count                      = count($result->get());

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

		$result                     = $result->get();
	
		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Display an admin
	 *
	 * @param admin id
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Entities\Admin::id($id)->first();

		if($result)
		{
			return response()->json( JSend::success($result->toArray())->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::fail(['ID Tidak Valid.']));
	}

	/**
	 * Store an admin
	 *
	 * @return Response
	 */
	public function store()
	{
		if(!Input::has('admin'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data admin.');
		}

		//1. Validate Admin Parameter
		$admin				= Input::get('admin');

		$admin_store		= $this->admin;
			
		$admin_store->fill($admin);

		if(!$admin_store->save())
		{
			return response()->json( JSend::error($admin_store->getError()->toArray())->asArray());
		}
		
		return response()->json( JSend::success($admin_store->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}
}
