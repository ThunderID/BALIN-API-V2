<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\StorePointInterface;

use App\Contracts\Policies\ValidatingReferralSistemInterface;
use App\Contracts\Policies\ProceedReferralSistemInterface;
use App\Contracts\Policies\EffectReferralSistemInterface;

class BalinManualStorePoint implements StorePointInterface 
{
	protected $point;
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
	public function fill(array $point)
	{
		$this->point 		= $point;
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
		$this->pre->validatepoint($this->point); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		// $this->pre->point['is_active']	= true;

		\DB::BeginTransaction();

		/** PROCESS */

		//2. Store Data point
		$this->pro->storepoint($this->point); 

		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//3. Return point Model Object
		$this->saved_data	= $this->pro->point;

		return true;
	}
}
