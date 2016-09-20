<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Entities\Customer;
use App\Entities\Sale;
use App\Entities\TransactionLog;
use App\Entities\Voucher;

use App\Contracts\CheckoutInterface;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\EffectTransactionInterface;

class BalinCheckout implements CheckoutInterface 
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
		$customer 						= Customer::findorfail($this->sale['user_id']);
		$sale 							= Sale::findornew($this->sale['id']);
		
		/** PREPROCESS */

		//1. Generate sale number
		$this->sale['ref_number']		= $this->pre->getsalenumber($sale); 

		//2. Validate Buyer
		$this->pre->validatebuyer($customer); 

		//3. Validate Stock, Price, Calculate Price main product
		$this->pre->validatesaleitem(isset($this->sale['transactiondetails']) ? $this->sale['transactiondetails'] : null); 

		//4. Validate Stock, Calculate Price of packing ornament
		if(isset($this->sale['transactionextensions']))
		{
			$this->pre->validatepackingornament($this->sale['transactionextensions']); 
		}

		//5. Validate shipping cost, calculate shipping cost address
		$this->pre->validateshippingaddress($this->sale['shipment']); 

		//6. Validate voucher
		$this->pre->validateshoppingvoucher(!isset($this->sale['voucher']) ? [] : $this->sale['voucher']); 

		//7. Validate Checkout Status
		$this->pre->validatecheckoutstatus($sale); 

		//8. Calculatepoint discount
		$this->pre->calculatepointdiscount($customer, $sale); 
		
		//9. Generate unique number
		$this->sale['unique_number']	= $this->pre->getuniquenumber($sale); 

		if($this->pre->errors->count())
		{
			$this->errors 				= $this->pre->errors;

			return false;
		}

		//10. set transact_at
		$this->sale['transact_at'] 		= \Carbon\Carbon::now()->format('Y-m-d H:i:s');

		//11. set shipping cost 
		$this->sale['shipping_cost']	= $this->pre->getshippingcost();

		//12. set voucher discount 
		$this->sale['voucher_discount']	= $this->pre->getvoucherdiscount();

		//------- Area of validating entry promo referral -------//

		//13. Get referral code
		if(!isset($this->sale['voucher']['id']) && isset($this->sale['voucher']['code']))
		{
			$this->pre_voucher->validatedownline($customer); 

			if($this->pre_voucher->errors->count())
			{
				$this->errors 				= $this->pre_voucher->errors;

				return false;
			}

		}

		//----- End Area of validating entry promo referral -----//

		\DB::BeginTransaction();

		/** PROCESS */

		//14. Store Data Transaksi
		$this->pro->store($this->sale); 
		
		//15. Store sale item
		$this->pro->storesaleitem($this->pro->sale, isset($this->sale['transactiondetails']) ? $this->sale['transactiondetails'] : null); 

		//16. Store packing ornament
		if(isset($this->sale['transactionextensions']))
		{
			$this->pro->storepackingornament($this->pro->sale, $this->sale['transactionextensions']); 
		}
		
		//17. Store Shipping Address
		$this->pro->shippingaddress($this->pro->sale, $this->sale['shipment']); 

		//18a. Reduce Quota Voucher
		if($this->pro->sale->voucher()->count())
		{
			$this->pro->creditquotavoucher($this->pro->sale->voucher, 1, 'Belanja #'.$this->sale['ref_number']); 
		}

		//------- Area of store entry promo referral -------//
		
		//18b. reduce and add point if voucher -eq promo referral
		if(!$this->pro->sale->voucher()->count() && isset($this->sale['voucher']['code']))
		{
			$promo_referral 		= Voucher::code($this->sale['voucher']['code'])->type('promo_referral')->first();
			
			//18bi. Store bonus for downline
			$this->pro_sale->storebonusesvoucher($customer, $promo_referral); 

			//18bii. requce upline quota
			$this->pro_sale->storequotavoucher($promo_referral, $customer); 

			if($this->pro_sale->errors->count())
			{
				\DB::rollback();

				$this->errors 		= $this->pro_sale->errors;

				return false;
			}
		}		

		//----- End Area of store entry promo referral -----//

		//18. Reduce Balin Point
		$this->pro->creditbalinpoint($customer, $this->pro->sale, $this->pre->getpointdiscount()); 

		//19. Store Log Transaksi
		$this->pro->updatestatus($this->pro->sale, 'wait');

		//20. Check bills
		if($this->pre->getbills() == 0)
		{
			$this->pro->updatestatus($this->pro->sale, 'paid');
		} 

		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::Commit();

		/** POST PROCESS */

		//21. Send Mail
		$this->post->sendmailinvoice($this->pro->sale);

		//22. Send Mail for bills
		if($this->pre->getbills() == 0)
		{
			$this->post->sendmailpaymentacceptance($this->pro->sale);
		}

		//23. Return Sale Model Object
		$this->saved_data	= $this->pro->sale;

		return true;
	}
}
