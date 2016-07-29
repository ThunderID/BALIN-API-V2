<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Entities\Sale;

use App\Contracts\PackingOrderInterface;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\EffectTransactionInterface;

use App\Services\Policies\ValidatingPayment;

class BalinPackingOrder implements PackingOrderInterface 
{
	protected $sale;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $pre_pay;
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
		$this->pre_pay	= new ValidatingPayment;
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
		$sale 							= Sale::id($this->sale['id'])->with(['shipment', 'shipment.address'])->first();

		/** PREPROCESS */

		//1. Validate Bills paid
		$this->pre_pay->validatebillshavepaid($sale); 

		if($this->pre_pay->errors->count())
		{
			$this->errors 		= $this->pre_pay->errors;

			return false;
		}

		//2. Validate Shipping address
		$this->pre->validateshippingaddress($sale['shipment']->toArray()); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}
		
		\DB::BeginTransaction();

		/** PROCESS */

		//3. set status packed
		$this->pro->updatestatus($sale, 'packed');

		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::Commit();

		//4. Return Sale Model Object
		$this->saved_data	= $this->pro->sale;

		return true;
	}
}
