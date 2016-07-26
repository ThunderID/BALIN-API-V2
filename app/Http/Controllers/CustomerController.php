<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of customer
 * 
 * @author cmooy
 */
class CustomerController extends Controller
{
	/**
	 * Display all customers
	 *
	 * @return Response
	 */
	public function index()
	{
		$result                     = new \App\Models\Customer;

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
					case 'referralcode':
						$result     = $result->orderby('code_referral', $value);
						break;
					case 'totalreference':
						$result     = $result->orderby('total_reference', $value);
						break;
					case 'totalpoint':
						$result     = $result->orderby('total_point', $value);
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

		$result                     = $result->with(['myreferrals', 'myreferrals.user'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display a customer
	 *
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Models\Customer::id($id)->with(['sales', 'myreferrals', 'myreferrals.user'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}
		
		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}
}
