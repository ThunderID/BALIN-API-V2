<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\ActivateAccountInterface;

use App\Contracts\Policies\ValidatingRegisterUserInterface;
use App\Contracts\Policies\ProceedRegisterUserInterface;
use App\Contracts\Policies\EffectRegisterUserInterface;

class BalinAccountActivate implements ActivateAccountInterface 
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
		$this->pre->validateactivationlink($this->customer); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */
		//2. Store acticvate customer
		$this->pro->activatinguser($this->pre->customer); 
		
		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//3. Return customer Model Object
		$this->saved_data		= $this->pro->customer;

		return true;
	}
}
