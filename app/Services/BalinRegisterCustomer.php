<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\RegisterCustomerInterface;

use App\Contracts\Policies\ValidatingRegisterUserInterface;
use App\Contracts\Policies\ProceedRegisterUserInterface;
use App\Contracts\Policies\EffectRegisterUserInterface;

class BalinRegisterCustomer implements RegisterCustomerInterface 
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
	function __construct(ValidatingRegisterUserInterface $pre, ProceedRegisterUserInterface $pro, EffectRegisterUserInterface $post)
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
		/** PREPROCESS */

		//1. Validate 
		$this->pre->validatecustomer($this->customer); 

		//2. Get referral code
		$this->pre->getreferralcode($this->customer); 

		//3. get activation link
		$this->pre->getactivationlink($this->pre->customer); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		$this->pre->customer['is_active']	= false;

		\DB::BeginTransaction();

		/** PROCESS */

		//4. Store Data Customer
		$this->pro->storecustomer($this->pre->customer); 
		//5. Store customer referral
		$this->pro->storereferral($this->pro->customer, $this->pre->customer); 
		
		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//6. Send mail for customer
		$this->post->sendactivationmail($this->pro->customer, $this->customer['client_id']);

		//7. Return customer Model Object
		$this->saved_data	= $this->pro->customer;

		return true;
	}
}
