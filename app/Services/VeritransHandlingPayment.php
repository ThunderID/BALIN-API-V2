<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Entities\User;
use App\Entities\Sale;
use App\Entities\TransactionLog;

use App\Contracts\HandlingPaymentInterface;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ValidatingPaymentInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\ProceedPaymentInterface;
use App\Contracts\Policies\EffectTransactionInterface;
use App\Contracts\Policies\EffectPaymentInterface;

class VeritransHandlingPayment implements HandlingPaymentInterface 
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

		switch (strtolower($this->sale['payment']['status'])) 
		{
			case 'settlement':
				//1. Validate bills is null
				$this->pre->validatebillshaventpaid($sale); 
				
				if($this->pre->errors->count())
				{
					$this->errors 		= $this->pre->errors;

					return false;
				}

				//2. Validate Shipping address
				$this->pre_sale->validateshippingaddress($this->sale['shipment']); 
				
				if($this->pre_sale->errors->count())
				{
					$this->errors 		= $this->pre_sale->errors;

					return false;
				}

				//3. Validate payment amount
				$this->pre->validatepaymentamount($sale, $this->sale['payment']); 
				
				if($this->pre->errors->count())
				{
					$this->errors 		= $this->pre->errors;

					return false;
				}

				\DB::BeginTransaction();
				
				//4. store payment
				$this->pro->storepayment($sale, $this->sale['payment']); 

				if($this->pro->errors->count())
				{
					\DB::rollback();

					$this->errors 		= $this->pro->errors;

					return false;
				}

				//5. update status bayar
				$this->pro_sale->updatestatus($sale, 'paid'); 
		
				//6. do something for upline
				$this->pro_sale->grantupline($this->pro_sale->sale); 

				if($this->pro_sale->errors->count())
				{
					\DB::rollback();

					$this->errors 		= $this->pro_sale->errors;

					return false;
				}

				\DB::commit();

				//7. kirim email bayar
				$this->post_sale->sendmailpaymentacceptance($this->pro_sale->sale, $this->sale['client_id']);

				//8. update return value
				$this->saved_data		= $this->pro_sale->sale;

				break;
			case 'deny': case 'canceled' :

				//1. Validate bills is null
				$this->pre->validatebillshaventpaid($sale); 

				\DB::BeginTransaction();

				/** PROCESS */

				//2. rollback point
				$this->pro_sale->revertbalinpoint($sale); 
				
				//3. set status cancel
				$this->pro_sale->updatestatus($sale, 'canceled');

				if($this->pro_sale->errors->count())
				{
					\DB::rollback();

					$this->errors 		= $this->pro_sale->errors;

					return false;
				}

				\DB::Commit();

				/** POST PROCESS */

				//4. Send Mail
				$this->post_sale->sendmailcancelorder($this->pro_sale->sale);

				//5. Return Sale Model Object
				$this->saved_data	= $this->pro_sale->sale;
				
				break;

			default :
				$this->saved_data		= $this->sale;
				break;
		}

		return true;
	}
}
