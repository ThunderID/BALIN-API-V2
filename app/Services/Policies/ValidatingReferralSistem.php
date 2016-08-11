<?php

namespace App\Services\Policies;

use App\Entities\Customer;
use App\Entities\Referral;
use App\Entities\Voucher;
use App\Entities\PointLog;
use App\Entities\UserInvitationLog;

use App\Contracts\Policies\ValidatingReferralSistemInterface;

use Illuminate\Support\MessageBag;

class ValidatingReferralSistem implements ValidatingReferralSistemInterface
{
	public $errors;
	
	public $referral;

	public $invitationlog;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 	= new MessageBag;
	}

	public function validateupline(array $customer)
	{
		if(!isset($customer['reference_code']))
		{
			$this->errors->add('Referral', 'Kode referral tidak boleh kosong');
		}

		$exists_ref 			= Referral::code($customer['reference_code'])->first();

		if(!$exists_ref)
		{
			$this->errors->add('Referral', 'Referral tidak valid');
		}

		if($exists_ref->quota <= 0)
		{
			$this->errors->add('Referral', 'Quota Referral sudah habis');
		}

		if($exists_ref->user_id == $customer['id'])
		{
			$this->errors->add('Referral', 'Tidak dapat menggunakan referral Anda');
		}

		$downline 			 	= PointLog::userid($exists_ref['user_id'])->referenceid($customer['id'])->first();

		if($downline)
		{
			$this->errors->add('Customer', 'Tidak dapat menggunakan referral code downline Anda');
		}

		$this->referral 		= $exists_ref;
	}

	public function validatedownline(Customer $customer)
	{
		if(!str_is($customer->reference_name, 'EMPTY'))
		{
			$this->errors->add('Referral', $customer['name'].' Sudah memiliki reference');
		}
	}

	public function validatepromoreferral(array $customer)
	{
		if(!isset($customer['reference_code']))
		{
			$this->errors->add('Referral', 'Kode referral tidak boleh kosong');
		}

		$exists_ref 			= Voucher::code($customer['reference_code'])->ondate('now')->type('promo_referral')->first();

		if(!$exists_ref)
		{
			$this->errors->add('Referral', 'Promo Referral tidak valid');
		}

		if($exists_ref && $exists_ref->quota <= 0)
		{
			$this->errors->add('Referral', 'Quota Promo Referral sudah habis');
		}
		
		$this->referral 		= $exists_ref;
	}

	public function validateinvitation(Referral $referral, array $customer)
	{
		$invitation				= UserInvitationLog::userid($referral['user_id'])->email($customer['email'])->first();

		if(!$invitation)
		{
			$this->errors->add('Referral', 'Invitation tidak valid');
		}

		$this->invitationlog 	= $invitation;
	}

	public function validatepoint(array $point)
	{
	}
}

