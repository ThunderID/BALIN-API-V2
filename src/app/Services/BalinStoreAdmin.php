<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\StoreAdminInterface;

use App\Contracts\Policies\ValidatingRegisterUserInterface;
use App\Contracts\Policies\ProceedRegisterUserInterface;
use App\Contracts\Policies\EffectRegisterUserInterface;

class BalinStoreAdmin implements StoreAdminInterface 
{
	protected $admin;
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
	public function fill(array $admin)
	{
		$this->admin 		= $admin;
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
		$this->pre->validateadmin($this->admin); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		// $this->pre->admin['is_active']	= true;

		\DB::BeginTransaction();

		/** PROCESS */

		//2. Store Data Admin
		$this->pro->storeadmin($this->admin); 

		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//3. Return Admin Model Object
		$this->saved_data	= $this->pro->admin;

		return true;
	}
}
