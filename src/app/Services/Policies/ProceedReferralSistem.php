<?php

namespace App\Services\Policies;

use App\Entities\Customer;
use App\Entities\Referral;
use App\Entities\Voucher;
use App\Entities\PointLog;
use App\Entities\QuotaLog;
use App\Entities\StoreSetting;
use App\Entities\UserInvitationLog;

use Carbon\Carbon;

use App\Contracts\Policies\ProceedReferralSistemInterface;

use Illuminate\Support\MessageBag;

class ProceedReferralSistem implements ProceedReferralSistemInterface
{
	public $errors;
	
	public $referral;

	public $point;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 	= new MessageBag;
	}

	public function storebonusesfordownline(Customer $customer, Referral $referral)
	{
		$gift					= StoreSetting::type('invitation_royalty')->Ondate('now')->first();

		if(!$gift)
		{
			$this->errors->add('Customer', ' Tidak dapat menambahkan referral saat ini');
		}

		$store					= StoreSetting::type('voucher_point_expired')->Ondate('now')->first();

		if($store)
		{
			$expired_at			= new Carbon($store->value);
		}
		else
		{
			$expired_at			= new Carbon('+ 3 months');
		}

		$point					=   [
										'user_id'			=> $customer['id'],
										'reference_id'		=> $referral['user_id'],
										'reference_type'	=> get_class($referral->user),
										'expired_at'		=> $expired_at->format('Y-m-d H:i:s'),
										'amount'			=> $gift->value,
										'notes'				=> 'Direferensikan '.$referral['user']['name'],
									];

		$point_data				= new PointLog;
		
		$point_data->fill($point);

		if(!$point_data->save())
		{
			$this->errors->add('Redeem', $point_data->getError());
		}

		$this->referral 		= $customer;
		$this->point 			= $point_data;
	}

	public function storebonusesforupline(Referral $referral, Customer $customer)
	{
		$gift					= StoreSetting::type('referral_royalty')->Ondate('now')->first();

		if(!$gift)
		{
			$this->errors->add('Customer', ' Tidak dapat menambahkan referral saat ini');
		}

		$store					= StoreSetting::type('voucher_point_expired')->Ondate('now')->first();

		if($store)
		{
			$expired_at			= new Carbon($store->value);
		}
		else
		{
			$expired_at			= new Carbon('+ 3 months');
		}

		$point					=   [
										'user_id'			=> $referral['user_id'],
										'reference_id'		=> $this->point['id'],
										'reference_type'	=> get_class($this->point),
										'expired_at'		=> $expired_at->format('Y-m-d H:i:s'),
										'amount'			=> $gift->value,
										'notes'				=> 'Mereferensikan '.$customer['name'],
									];

		$point_data				= new PointLog;
		
		$point_data->fill($point);

		if(!$point_data->save())
		{
			$this->errors->add('Redeem', $point_data->getError());
		}

		$this->referral 		= $customer;
	}

	public function storequotaupline(Referral $referral, Customer $customer)
	{
		$quotalog				= new QuotaLog;

		$quotalog->fill([
				'voucher_id'	=> $referral['id'],
				'amount'		=> -1,
				'notes'			=> 'Mereferensikan '.$customer['name'],
			]);

		if(!$quotalog->save())
		{
			$this->errors->add('Redeem', $quotalog->getError());
		}

		$this->referral 		= $customer;
	}

	public function storebonusesvoucher(Customer $customer, Voucher $voucher)
	{
		$store					= StoreSetting::type('voucher_point_expired')->Ondate('now')->first();

		if($store)
		{
			$expired_at			= new Carbon($store->value);
		}
		else
		{
			$expired_at			= new Carbon('+ 3 months');
		}

		$point					=   [
										'user_id'			=> $customer['id'],
										'reference_id'		=> $voucher['id'],
										'reference_type'	=> get_class($voucher),
										'expired_at'		=> $expired_at->format('Y-m-d H:i:s'),
										'amount'			=> $voucher->value,
										'notes'				=> 'Direferensikan '.$voucher['user']['name'],
									];

		$point_data				= new PointLog;
		
		$point_data->fill($point);

		if(!$point_data->save())
		{
			$this->errors->add('Redeem', $point_data->getError());
		}

		$this->referral 		= $customer;
	}

	public function storequotavoucher(Voucher $voucher, Customer $customer)
	{
		$quotalog				= new QuotaLog;

		$quotalog->fill([
				'voucher_id'	=> $voucher['id'],
				'amount'		=> -1,
				'notes'			=> 'Mereferensikan '.$customer['name'],
			]);

		if(!$quotalog->save())
		{
			$this->errors->add('Redeem', $quotalog->getError());
		}

		$this->referral 		= $customer;
	}

	public function storeinvitationlog(UserInvitationLog $invitationlog)
	{
		$invitationlog->is_used	= true;

		if(!$invitationlog->save())
		{
			$this->errors->add('Redeem', $invitationlog->getError());
		}
	}

	public function storepoint(array $point)
	{
		$stored_point					= PointLog::findornew($point['id']);
		
		$stored_point->fill($point);

		if(!$stored_point->save())
		{
			$this->errors->add('Redeem', $stored_point->getError());
		}

		$this->point					= $stored_point;
	}

}

