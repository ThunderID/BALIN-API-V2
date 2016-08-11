<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Entities\User;
use App\Entities\Purchase;
use App\Entities\TransactionLog;

use App\Contracts\RollbackStockInterface;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\EffectTransactionInterface;

class BalinRollbackStock implements RollbackStockInterface 
{
	protected $purchase;
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
	 * RollbackStock
	 *
	 * 1. Call Class fill
	 * 
	 * @return Response
	 */
	public function fill(array $purchase)
	{
		$this->purchase 		= $purchase;
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
		$purchase 						= Purchase::findornew($this->purchase['id']);
		
		/** PREPROCESS */

		//1. Validate rollback
		$this->pre->validaterollbackitem($purchase['transactiondetails']->toArray()); 

		if($this->pre->errors->count())
		{
			$this->errors 					= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//2. Store Log Transaksi
		$this->pro->updatestatus($purchase, 'canceled');

		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::Commit();

		//3. Return purchase Model Object
		$this->saved_data	= $this->pro->sale;

		return true;
	}
}
