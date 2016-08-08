<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\EntryUplineInterface;

use App\Contracts\Policies\ValidatingReferralSistemInterface;
use App\Contracts\Policies\ProceedReferralSistemInterface;
use App\Contracts\Policies\EffectReferralSistemInterface;

use App\Entities\Customer;

class BalinEntryReferral implements EntryUplineInterface 
{
	protected $customer;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $post;
	protected $pro;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(ValidatingReferralSistemInterface $pre, ProceedReferralSistemInterface $pro, EffectReferralSistemInterface $post)
	{
		$this->errors 	= new MessageBag;
		$this->pre 		= $pre;
		$this->pro 		= $pro;
		$this->post 	= $post;
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
		$customer 			= Customer::find($this->customer['id']);

		/** PREPROCESS */

		//1. Validate 
		$this->pre->validateupline($this->customer); 

		//2. Get referral code
		$this->pre->validatedownline($customer); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//4. Store bonus for downline
		$this->pro->storebonusesfordownline($customer, $this->pre->referral); 

		//5. Store bonus for upline
		$this->pro->storebonusesforupline($this->pre->referral, $customer); 

		//6. requce upline quota
		$this->pro->storequotaupline($this->pre->referral, $customer); 

		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//7. Return customer Model Object
		$this->saved_data	= Customer::id($this->customer['id'])->first();

		return true;
	}
}
