<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Entities\User;
use App\Entities\Sale;
use App\Entities\TransactionLog;

use App\Contracts\CheckoutInterface;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\EffectTransactionInterface;

use App\Services\Policies\ProceedCustomer;
use App\Services\Policies\ProceedPayment;

class ThirdPartyCheckout implements CheckoutInterface 
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
		$this->pro_cust	= new ProceedCustomer;
		$this->pro_pay	= new ProceedPayment;
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
		$sale 							= Sale::findornew($this->sale['id']);
		
		/** PREPROCESS */

		//1. Generate sale number
		$this->sale['ref_number']		= $this->pre->getsalenumber($sale); 

		//2. Validate Stock, Price, Calculate Price main product
		$this->pre->validatesaleitem($this->sale['transactiondetails']); 

		//4. Validate shipping cost, calculate shipping cost address
		$this->pre->validateshippingaddress($this->sale['shipment']); 

		//5. Validate Checkout Status
		$this->pre->validatecheckoutstatus($sale); 

		//6. Generate unique number
		$this->sale['unique_number']	= 0; 

		//7. set transact_at
		$this->sale['transact_at'] 		= \Carbon\Carbon::now()->format('Y-m-d H:i:s');

		//8. set total payment
		$this->sale['payment']['amount']= $this->pre->getsubtotal();

		\DB::BeginTransaction();

		/** PROCESS */

		//9. Store Data customer
		$this->pro_cust->storecustomer($this->sale['user']); 

		if($this->pro_cust->errors->count())
		{
			$this->errors 				= $this->pro_cust->errors;

			return false;
		}

		//10. set customer
		$this->sale['user_id']			= $this->pro_cust->customer['id'];

		//11. Store Data Transaksi
		$this->pro->store($this->sale); 
		
		//12. Store sale item
		$this->pro->storesaleitem($this->pro->sale, $this->sale['transactiondetails']); 

		//13. set owner of address
		$this->sale['shipment']['address']['owner_id']		= $this->pro_cust->customer['id'];
		$this->sale['shipment']['address']['owner_type']	= get_class($this->pro_cust->customer);

		//14. Store Shipping Address
		$this->pro->shippingaddress($this->pro->sale, $this->sale['shipment']); 

		//15. Store payment
		$this->pro_pay->storepayment($this->pro->sale, $this->sale['payment']); 

		if($this->pro_pay->errors->count())
		{
			$this->errors 				= $this->pro_pay->errors;

			return false;
		}

		//16. Update status
		$this->pro->updatestatus($this->pro->sale, 'paid');

		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::Commit();

		//17. Return Sale Model Object
		$this->saved_data	= $this->pro->sale;

		return true;
	}
}
