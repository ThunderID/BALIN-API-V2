<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\StoreSettingInterface;

use App\Contracts\Policies\ValidatingStoreSettingInterface;
use App\Contracts\Policies\ProceedStoreSettingInterface;
use App\Contracts\Policies\EffectStoreSettingInterface;

use App\Entities\StoreSetting;

class BalinStoreWebPageSetting implements StoreSettingInterface 
{
	protected $storesetting;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $post;
	protected $pro;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(ValidatingStoreSettingInterface $pre, ProceedStoreSettingInterface $pro, EffectStoreSettingInterface $post)
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
	public function fill(array $storesetting)
	{
		$this->storesetting 		= $storesetting;
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

		//1. Validate StoreSetting
		$this->pre->validatestorepage($this->storesetting); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//2. store StoreSetting
		$this->pro->storestorepage($this->storesetting); 

		if($this->pro->errors->count())
		{
			\DB::rollback();
			
			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//3. Return StoreSetting Model Object
		$this->saved_data	= $this->pro->storesetting;

		return true;
	}
}
