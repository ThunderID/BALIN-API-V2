<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Entities\Sale;
use App\Models\User;

use App\Contracts\HandlingPaymentInterface;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ValidatingPaymentInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\ProceedPaymentInterface;
use App\Contracts\Policies\EffectTransactionInterface;
use App\Contracts\Policies\EffectPaymentInterface;

class BalinPointHandlingPayment implements HandlingPaymentInterface 
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
	
		//3. Validate bills has not paid
		$this->pre->validatebillshaventpaid($sale); 
		
		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}
				
		//4. Calculate point discount
		$this->pre_sale->calculatepointdiscount($customer, $sale); 

		if($this->pre_sale->errors->count())
		{
			$this->errors 		= $this->pre_sale->errors;

			return false;
		}

		\DB::BeginTransaction();
				
		//5. reduce balin point
		$this->pro_sale->creditbalinpoint($customer, $sale, $this->pre_sale->getpointdiscount()); 

		//6. Check bills
		if($this->pre_sale->getpointdiscount() == $sale->bills)
		{
			$this->pro_sale->updatestatus($sale, 'paid');
		} 

		if($this->pro_sale->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro_sale->errors;

			return false;
		}

		\DB::commit();

		//7. kirim email bayar
		$this->post_sale->sendmailpaymentacceptance($this->pro_sale->sale, $this->sale['client_id']);

		$this->saved_data		= $this->pro_sale->sale;

		return true;
	}
}
