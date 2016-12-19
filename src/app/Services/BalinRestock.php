<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Entities\Purchase;
use App\Entities\Supplier;

use App\Contracts\RestockInterface;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\EffectTransactionInterface;

class BalinRestock implements RestockInterface 
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
	 * Restock
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
		$supplier 						= Supplier::findorfail($this->purchase['supplier_id']);
		$purchase 						= Purchase::findornew($this->purchase['id']);
		
		/** PREPROCESS */

		//1. Generate purchase number
		$this->purchase['type']			= 'buy'; 
		$this->purchase['ref_number']	= $this->pre->getpurchasenumber($purchase); 

		//2. Validate quantity
		$this->pre->validaterollbackitem($purchase['transactiondetails']->toArray()); 

		//3. Validate Buyer
		$this->pre->validatesupplier($supplier); 

		//4. Validate Stock, Price, Calculate Price main product
		$this->pre->validatepurchaseitem($this->purchase['transactiondetails']); 

		if($this->pre->errors->count())
		{
			$this->errors 					= $this->pre->errors;

			return false;
		}

		//5. set transact_at
		if(!isset($this->purchase['transact_at']))
		{
			$this->purchase['transact_at']	= \Carbon\Carbon::now()->format('Y-m-d H:i:s');
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//6. Store Data Transaksi
		$this->pro->storepurchase($this->purchase); 
		
		//7. Store purchase item
		$this->pro->storepurchaseitem($this->pro->sale, $this->purchase['transactiondetails']); 

		//8. Store Log Transaksi
		$this->pro->updatestatus($this->pro->sale, 'delivered');

		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::Commit();

		//9. Return purchase Model Object
		$this->saved_data	= $this->pro->sale;

		return true;
	}
}
