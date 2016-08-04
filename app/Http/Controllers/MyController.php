<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Entities\StoreSetting;

use Carbon\Carbon;

class MyController extends Controller
{
	/**
	 * Display a customer by me
	 *
	 * @return Response
	 */
	public function detail($user_id = null)
	{
		$result                 = \App\Entities\Customer::id($user_id)->with(['myreferrals', 'myreferrals.user'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}
		
		return response()->json( JSend::fail(['ID Tidak Valid.']));
	}

	/**
	 * Display my points
	 *
	 * @return Response
	 */
	public function points($user_id = null)
	{
		$result                     = \App\Entities\PointLog::summary($user_id)->orderby('created_at', 'desc');

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

		$result                     = $result->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}


	/**
	 * Display my invitations
	 *
	 * @return Response
	 */
	public function invitations($user_id = null)
	{
		$result                     = \App\Entities\UserInvitationLog::userid($user_id)->orderby('created_at', 'desc');

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

		$result                     = $result->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display my addresses
	 *
	 * @return Response
	 */
	public function addresses($user_id = null)
	{
		$result                     = \App\Entities\Address::ownerid($user_id)->ownertype(['App\Entities\Customer', 'App\Models\Customer']);

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
	 * Register customer
	 *
	 * @return Response
	 */
	public function store($user_id = null)
	{
		if(!Input::has('customer'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data customer.');
		}

		$customer                   = Input::get('customer');

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Validate User Parameter
		if(is_null($customer['id']))
		{
			$is_new                 = true;
		}
		else
		{
			$is_new                 = false;
		}

		$customer_rules             =   [
											'name'                          => 'required|max:255',
											'email'                         => 'max:255|unique:users,email,'.(!is_null($customer['id']) ? $customer['id'] : ''),
											'password'                      => 'max:255',
											'sso_id'                        => '',
											'sso_media'                     => 'in:facebook',
											'sso_data'                      => '',
											'gender'                        => 'in:male,female',
											'date_of_birth'                 => 'date_format:"Y-m-d H:i:s"',
										];

		//1a. Get original data
		$customer_data              = \App\Entities\Customer::findornew($customer['id']);

		//1b. Validate Basic Customer Parameter
		$validator                  = Validator::make($customer, $customer_rules);

		if (!$validator->passes())
		{
			$errors->add('Customer', $validator->errors());
		}
		else
		{
			//if validator passed, save customer
			$customer_data           = $customer_data->fill($customer);

			if(!$customer_data->save())
			{
				$errors->add('Customer', $customer_data->getError());
			}
		}

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_customer                 = \App\Entities\Customer::id($user_id)->with(['myreferrals', 'myreferrals.user'])->first()->toArray();

		return new JSend('success', (array)$final_customer);
	}

	/**
	 * Redeem code
	 *
	 * @return Response
	 */
	public function redeem($user_id = null)
	{
		if(!Input::has('code'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data code.');
		}

		$code                       = Input::get('code');

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Check Link
		$voucher_data              = \App\Entities\Referral::code($code)->first();
		if(!$voucher_data)
		{
			$voucher_data			= \App\Entities\Voucher::code($code)->type('promo_referral')->ondate('now')->first();
		}

		if(!$voucher_data)
		{
			$errors->add('Redeem', 'Code tidak valid.');
		}
		elseif($voucher_data->quota <= 0)
		{
			$errors->add('Redeem', 'Quota referral sudah habis.');
		}
		else
		{
			$store                      = StoreSetting::type('voucher_point_expired')->Ondate('now')->first();

			if($store)
			{
				$expired_at             = new Carbon($store->value);
			}
			else
			{
				$expired_at             = new Carbon('+ 3 months');
			}

			//if validator passed, save voucher
			if($voucher_data['type']=='referral')
			{
				$reference_id 			= $voucher_data['user_id'];
				$reference_type			= 'App\Entities\User';
			}
			else
			{
				$reference_id 			= $voucher_data['id'];
				$reference_type			= 'App\Entities\Voucher';
			}

			$point                  =   [
											'user_id'               => $user_id,
											'reference_id'        	=> $reference_id,
											'reference_type'        => $reference_type,
											'expired_at'            => $expired_at->format('Y-m-d H:i:s'),
										];

			$point_data             = new \App\Entities\PointLog;
			
			$point_data->fill($point);

			if(!$point_data->save())
			{
				$errors->add('Redeem', $point_data->getError());
			}
		}

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_costumer                 = \App\Entities\Customer::id($user_id)->with(['myreferrals', 'myreferrals.user'])->first()->toArray();

		return new JSend('success', (array)$final_costumer);
	}


	/**
	 * Invite friend
	 *
	 * @return Response
	 */
	public function invite($user_id = null)
	{
		if(!Input::has('invitations'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data invitations.');
		}

		$invitations				= Input::get('invitations');

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. Store mail
		if(!$errors->count())
		{
			foreach ($invitations as $key => $value) 
			{
				if(!$errors->count())
				{
					$log_data		= new \App\Entities\UserInvitationLog;

					$log_rules		=   [
												'email'			=> 'required|email',
											];

					$validator			= Validator::make($value, $log_rules);

					//if there was log and validator false
					if (!$validator->passes())
					{
						$errors->add('log', $validator->errors());
					}
					else
					{
						$value['user_id']		= $user_id;

						$log_data				= $log_data->fill($value);

						if(!$log_data->save())
						{
							$errors->add('Log', $log_data->getError());
						}
					}
				}
			}
		}
		
		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_costumer                 = \App\Entities\Customer::id($user_id)->with(['myreferrals', 'myreferrals.user'])->first()->toArray();

		return new JSend('success', (array)$final_costumer);
	}
}
