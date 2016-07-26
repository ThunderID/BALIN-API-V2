<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of admin
 * 
 * @author cmooy
 */
class AdminController extends Controller
{
	/**
	 * Display all admins
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index()
	{
		$result                 = new \App\Models\Admin;

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
		
		$count                      = count($result->get(['id']));

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

		$result                     = $result->with(['audits'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display an admin
	 *
	 * @param admin id
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Models\Admin::id($id)->with(['audits'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
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

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Validate Admin Parameter
		$admin                      = Input::get('admin');
		
		if(is_null($admin['id']))
		{
			$is_new                 = true;
		}
		else
		{
			$is_new                 = false;
		}

		$admin_rules                =   [
											'name'                          => 'required|max:255',
											'email'                         => 'required|max:255|unique:users,email,'.(!is_null($admin['id']) ? $admin['id'] : ''),
											'role'                          => 'required|in:admin,store_manager,staff',
											'is_active'                     => 'boolean',
											'gender'                        => 'in:male,female',
											'date_of_birth'                 => 'date_format:"Y-m-d H:i:s"',
										];

		//1a. Get original data
		$admin_data                 = \App\Models\Admin::findornew($admin['id']);

		//1b. Validate Basic Admin Parameter
		$validator                  = Validator::make($admin, $admin_rules);

		if (!$validator->passes())
		{
			$errors->add('Admin', $validator->errors());
		}
		else
		{
			//if validator passed, save admin
			$admin_data           = $admin_data->fill($admin);

			if(!$admin_data->save())
			{
				$errors->add('Admin', $admin_data->getError());
			}
		}

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_admin                 = \App\Models\Admin::id($admin_data['id'])->with(['audits'])->first()->toArray();

		return new JSend('success', (array)$final_admin);
	}
}
