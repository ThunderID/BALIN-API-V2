<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\RegisterCustomerInterface;

use App\Contracts\Policies\ValidatingRegisterUserInterface;
use App\Contracts\Policies\ProceedRegisterUserInterface;
use App\Contracts\Policies\EffectRegisterUserInterface;

use App\Services\Policies\ValidatingReferralSistem;
use App\Services\Policies\ProceedReferralSistem;

class BalinRegisterCustomerByInvitation implements RegisterCustomerInterface 
{
	protected $customer;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $post;
	protected $pro;
	protected $pre_voucher;
	protected $pro_voucher;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(ValidatingRegisterUserInterface $pre, ProceedRegisterUserInterface $pro, EffectRegisterUserInterface $post)
	{
		$this->errors 		= new MessageBag;
		$this->pre 			= $pre;
		$this->pro 			= $pro;
		$this->post 		= $post;
		$this->pre_voucher 	= new ValidatingReferralSistem;
		$this->pro_voucher 	= new ProceedReferralSistem;
	}

	/**
	 * return errors
	 *
	 * @return MessageBag
	 **/
	function getError()
	{
		return $this->errors;
	}

	/**
	 * return saved_data
	 *
	 * @return saved_data
	 **/
	function getData()
	{
		return $this->saved_data;
	}

	/**
	 * Checkout
	 *
	 * 1. Call Class fill
	 * 
	 * @return Response
	 */
	public function fill(array $customer)
	{
		$this->customer 		= $customer;
	}

	/**
	 * Save
	 *
	 * Here's the workflow
	 * 
	 * @return Response
	 */
	public function save()
	{
		/** PREPROCESS */

		//1. Validate 
		$this->pre->validatecustomer($this->customer); 
		
		//2. Validate voucher 
		$this->pre_voucher->validateupline($this->customer); 
		
		//3. Validate invitation 
		$this->pre_voucher->validateinvitation($this->pre_voucher->referral, $this->customer); 

		if($this->pre_voucher->errors->count())
		{
			$this->errors 		= $this->pre_voucher->errors;

			return false;
		}

		//4. Get referral code
		$this->pre->getreferralcode($this->customer); 

		//5. get activation link
		$this->pre->getactivationlink($this->pre->customer); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		$this->pre->customer['is_active']	= false;

		\DB::BeginTransaction();

		/** PROCESS */

		//6. Store Data Customer
		$this->pro->storecustomer($this->pre->customer); 

		//7. Store customer referral
		$this->pro->storereferral($this->pro->customer, $this->pre->customer); 
		
		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		//8. Store bonus for downline
		$this->pro_voucher->storebonusesfordownline($this->pro->customer, $this->pre_voucher->referral); 

		//9. Store bonus for upline
		$this->pro_voucher->storebonusesforupline($this->pre_voucher->referral, $this->pro->customer); 

		//10. requce upline quota
		$this->pro_voucher->storequotaupline($this->pre_voucher->referral, $this->pro->customer); 
		
		//11
		$this->pro_voucher->storeinvitationlog($this->pre_voucher->invitationlog); 

		if($this->pro_voucher->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro_voucher->errors;

			return false;
		}

		\DB::commit();

		//12. Send mail for customer
		$this->post->sendactivationmail($this->pro->customer);

		//13. Return customer Model Object
		$this->saved_data	= $this->pro->customer;

		return true;
	}
}
