<?php

namespace App\Services\Policies;

use App\Entities\Customer;
use App\Entities\Referral;
use App\Entities\QuotaLog;
use App\Entities\PointLog;
use App\Entities\StoreSetting;
use Carbon\Carbon;

use App\Contracts\Policies\ProceedRegisterUserInterface;

use Illuminate\Support\MessageBag;

class ProceedRegisterUser implements ProceedRegisterUserInterface
{
	public $errors;
	
	public $customer;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 	= new MessageBag;
	}

	public function storecustomer(array $customer)
	{
		$stored_customer			= Customer::findornew($customer['id']);
		$stored_customer->fill($customer);

		if(!$stored_customer->save())
		{
			$this->errors->add('Customer', $stored_customer->getError());
		}

		$this->customer		= $stored_customer;
	}

	public function storereferral(Customer $customer, array $referral)
	{
		$exists_referral		= Referral::userid($customer['id'])->first();

		if($exists_referral)
		{
			$stored_referral 	= $exists_referral;
		}
		else
		{
			$stored_referral	= new Referral;
		}

		$quota 					= StoreSetting::type('first_quota')->Ondate('now')->first();

		if(!$quota)
		{
			$this->errors->add('Customer', 'Tidak dapat melakukan registrasi saat ini.');
		}

		$stored_referral->fill(['code' => $referral['referral_code'], 'type' => 'referral', 'value' => 0]);

		if(!$stored_referral->save())
		{
			$this->errors->add('Customer', $stored_referral->getError());
		}
		else
		{
			$newquota 				= new QuotaLog;
			$newquota->fill([
				'voucher_id'		=> $stored_referral['id'],
				'amount'			=> $quota->value,
				'notes'				=> 'Hadiah registrasi',
				]);

			if(!$newquota->save())
			{
				$this->errors->add('Customer', $newquota->getError());
			}
		}

		$this->customer			= Customer::find($customer['id']);
	}


	public function activatinguser(Customer $customer)
	{
		$customer->activation_link	= '';
		$customer->is_active		= true;

		if(!$customer->save())
		{
			$this->errors->add('Customer', $customer->getError());
		}

		//give welcome gift
		$gift                    		= StoreSetting::type('welcome_gift')->Ondate('now')->first();

		$store                    		= StoreSetting::type('voucher_point_expired')->Ondate('now')->first();

		if($gift)
		{
			if($store)
			{
				$expired_at 			= new Carbon($store->value);
			}
			else
			{
				$expired_at 			= new Carbon('+ 3 months');
			}

			$point 						= new PointLog;

			$point->fill([
					'user_id'			=> $customer->id,
					'amount'			=> $gift->value,
					'expired_at'		=> $expired_at->format('Y-m-d H:i:s'),
					'notes'				=> 'Welcome Gift dari BALIN',
				]);

			if(!$point->save())
			{
				$this->errors->add('Customer', $point->getError());
			}
		}

		$this->customer		= Customer::find($customer['id']);
	}

}

