<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\DeleteSupplierInterface;

use App\Contracts\Policies\ValidatingSupplierInterface;
use App\Contracts\Policies\ProceedSupplierInterface;
use App\Contracts\Policies\EffectSupplierInterface;

use App\Entities\Supplier;

class BalinDeleteSupplier implements DeleteSupplierInterface 
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
	 * Save
	 *
	 * Here's the workflow
	 * 
	 * @return Response
	 */
	public function delete(Supplier $supplier)
	{
		$this->supplier 			= $supplier->toArray();
		
		/** PREPROCESS */

		//1. Validate Supplier
		$this->pre->validatedeletesupplier($supplier); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//2. Delete Suppliers
		$this->pro->deletesupplier($supplier); 

		if($this->pro->errors->count())
		{
			\DB::rollback();
			
			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//3. Return Supplier Model Object
		$this->saved_data	= $this->supplier;

		return true;
	}
}
