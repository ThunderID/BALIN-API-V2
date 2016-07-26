<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Models\User;
use App\Entities\Sale;
use App\Models\TransactionLog;

use App\Contracts\AddToCartInterface;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\EffectTransactionInterface;

class BalinAddToCart implements AddToCartInterface 
{
	protected $sale;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $post;
	protected $pro;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(ValidatingTransactionInterface $pre, ProceedTransactionInterface $pro, EffectTransactionInterface $post)
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
	public function fill(array $sale)
	{
		$this->sale 		= $sale;
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
		$customer 						= User::findorfail($this->sale['user_id']);
	
		/** PREPROCESS */

		//1. Validate Previous Transaction
		$this->pre->validateprevioustransaction($customer); 

		//2. Validate Buyer
		$this->pre->validatebuyer($customer); 

		//3. Validate Stock With Quantity
		$this->pre->validatesaleitem($this->sale['transactiondetails']); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */
		//4. Store Data Transaksi
		$this->pro->store($this->sale); 

		//5. Store sale item
		$this->pro->storesaleitem($this->pro->sale, $this->sale['transactiondetails']); 
		
		//6. Store Log Transaksi
		$this->pro->updatestatus($this->pro->sale, 'cart'); 
		
		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//7. Return Sale Model Object
		$this->saved_data	= $this->pro->sale;

		return true;
	}
}
