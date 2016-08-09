<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Libraries\JSend;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Entities\StoreSetting;
use App\Entities\Voucher;
use App\Entities\Referral;
use App\Entities\Customer;

use App\Services\BalinEntryReferral;
use App\Services\BalinEntryPromoReferral;
use App\Services\BalinInviteFriend;


class MyController extends Controller
{
	public function __construct(Request $request, BalinEntryReferral $referral, BalinEntryPromoReferral $promo_referral, BalinInviteFriend $invitation)
	{
		$this->request 					= $request;
		$this->referral 				= $referral;
		$this->promo_referral 			= $promo_referral;
		$this->invitation 				= $invitation;
	}

	/**
	 * Display a customer by me
	 *
	 * @return Response
	 */
	public function detail($user_id = null)
	{
		$result                 = Customer::id($user_id)->with(['myreferrals', 'myreferrals.user'])->first();

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
		$customer_data              = Customer::findornew($customer['id']);

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
		
		$final_customer                 = Customer::id($user_id)->with(['myreferrals', 'myreferrals.user'])->first()->toArray();

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

		$code                       = Input::only('code');
		$customer 					= Customer::findorfail($user_id);
		$referral					= Referral::code($code['code'])->first();

		if(!$referral)
		{
			$promo_ref 				= Voucher::code($code['code'])->ondate('now')->type(['promo_referral'])->first();
			
			if(!$promo_ref)
			{
				return new JSend('error', (array)Input::all(), 'Code tidak valid.');
			}
			else
			{
				$referral_store 	= $this->promo_referral;
			}
		}
		else
		{
			$referral_store			= $this->referral;
		}


		//1. Check Link
		$customer 					= $customer->toArray();
		$customer['reference_code']	= $code['code'];

		$referral_store->fill($customer);

		if(!$referral_store->save())
		{
			return response()->json( JSend::error($referral_store->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($referral_store->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
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

		$user['id'] 			= $user_id;
		$user['friends'] 		= Input::get('invitations');

		$this->invitation->fill($user);

		if(!$this->invitation->save())
		{
			return response()->json( JSend::error($this->invitation->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success(['Undangan terkirim']))
					->setCallback($this->request->input('callback'));
	}
}
