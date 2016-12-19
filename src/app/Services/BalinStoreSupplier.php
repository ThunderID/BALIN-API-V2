<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\StoreSupplierInterface;

use App\Contracts\Policies\ValidatingSupplierInterface;
use App\Contracts\Policies\ProceedSupplierInterface;
use App\Contracts\Policies\EffectSupplierInterface;

use App\Entities\Supplier;

class BalinStoreSupplier implements StoreSupplierInterface 
{
	protected $supplier;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $post;
	protected $pro;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(ValidatingSupplierInterface $pre, ProceedSupplierInterface $pro, EffectSupplierInterface $post)
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
	public function fill(array $supplier)
	{
		$this->supplier 		= $supplier;
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

		//1. Validate Supplier
		$this->pre->validatesupplier($this->supplier); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//2. store Supplier
		$this->pro->storesupplier($this->supplier); 

		//3. store images
		$this->pro->storeimage($this->pro->supplier, $this->supplier['images']); 

		if($this->pro->errors->count())
		{
			\DB::rollback();
			
			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//4. Return Supplier Model Object
		$this->saved_data	= $this->pro->supplier;

		return true;
	}
}
