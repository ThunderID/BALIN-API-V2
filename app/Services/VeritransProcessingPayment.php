<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Entities\User;
use App\Entities\Sale;
use App\Entities\TransactionLog;

use App\Contracts\ProcessingPaymentInterface;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\ValidatingPaymentInterface;
use App\Contracts\Policies\ProceedPaymentInterface;
use App\Contracts\Policies\EffectPaymentInterface;

class VeritransProcessingPayment implements ProcessingPaymentInterface 
{
	protected $sale;
	protected $errors;
	protected $saved_data;
	protected $pre_sale;
	protected $pro_sale;
	protected $pre;
	protected $post;
	protected $pro;

	/**
	 * construct function, iniate error
	 *
	 */
	public function __construct(ValidatingTransactionInterface $pre_sale, ProceedTransactionInterface $pro_sale, ValidatingPaymentInterface $pre, ProceedPaymentInterface $pro, EffectPaymentInterface $post)
	{
		$this->errors 	= new MessageBag;
		$this->pre_sale	= $pre_sale;
		$this->pro_sale	= $pro_sale;
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
		$sale				= Sale::id($this->sale['id'])->with(['shipment', 'shipment.address'])->first();
	
		/** PREPROCESS */

		//1. Validate Transaction is checked
		$this->pre_sale->validatecheckedoutstatus($sale); 

		//2. Validate shipping address
		$this->pre_sale->validateshippingaddress($sale['shipment']->toArray()); 

		if($this->pre_sale->errors->count())
		{
			$this->errors 		= $this->pre_sale->errors;

			return false;
		}

		//3. Validate bills > 0
		$this->pre->validatebillshaventpaid($sale); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}


		\DB::BeginTransaction();

		/** PROCESS */
		//4. Store Log Transaksi
		$this->pro_sale->updatestatus($sale, 'veritrans_processing_payment'); 
		
		if($this->pro_sale->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro_sale->errors;

			return false;
		}

		\DB::commit();

		//5. Return Sale Model Object
		$this->saved_data	= $this->pro_sale->sale;

		return true;
	}
}
