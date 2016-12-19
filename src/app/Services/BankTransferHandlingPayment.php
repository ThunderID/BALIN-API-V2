<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Entities\Sale;
use App\Entities\User;

use App\Contracts\HandlingPaymentInterface;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ValidatingPaymentInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\ProceedPaymentInterface;
use App\Contracts\Policies\EffectTransactionInterface;
use App\Contracts\Policies\EffectPaymentInterface;

class BankTransferHandlingPayment implements HandlingPaymentInterface 
{
	protected $sale;
	protected $errors;
	protected $saved_data;
	protected $pre_sale;
	protected $pre;
	protected $pro_sale;
	protected $pro;
	protected $post;
	protected $post_sale;

	/**
	 * construct function, iniate error
	 *
	 */
public function __construct(ValidatingTransactionInterface $pre_sale, ValidatingPaymentInterface $pre, ProceedTransactionInterface $pro_sale, ProceedPaymentInterface $pro, EffectTransactionInterface $post_sale, EffectPaymentInterface $post)
	{
		$this->errors 		= new MessageBag;
		$this->pre_sale		= $pre_sale;
		$this->pre 			= $pre;
		$this->pro_sale		= $pro_sale;
		$this->pro 			= $pro;
		$this->post_sale	= $post_sale;
		$this->post 		= $post;
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
		$sale 							= Sale::find($this->sale['id']);
		$customer 						= User::findorfail($this->sale['user_id']);

		//1. validate that transaction is checked out
		$this->pre_sale->validatecheckedoutstatus($sale); 
		
		//2. Validate Shipping address
		$this->pre_sale->validateshippingaddress($this->sale['shipment']); 
	

		if($this->pre_sale->errors->count())
		{
			$this->errors 		= $this->pre_sale->errors;

			return false;
		}

		//3. Validate bills has not paid
		$this->pre->validatebillshaventpaid($sale); 
		
		//4. Validate payment amount
		$this->pre->validatepaymentamount($sale, $this->sale['payment']); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();
				
		//5. store payment
		$this->pro->storepayment($sale, $this->sale['payment']); 

		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		//6. update status bayar
		$this->pro_sale->updatestatus($sale, 'paid'); 

		//7. do something for upline
		$this->pro_sale->grantupline($this->pro_sale->sale); 

		if($this->pro_sale->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro_sale->errors;

			return false;
		}

		\DB::commit();

		//8. kirim email bayar
		$this->post_sale->sendmailpaymentacceptance($this->pro_sale->sale);

		$this->saved_data		= $this->pro_sale->sale;

		return true;
	}
}
