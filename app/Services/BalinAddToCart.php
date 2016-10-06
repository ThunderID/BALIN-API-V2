<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Entities\Customer;
use App\Entities\Sale;
use App\Entities\Voucher;
use App\Entities\TransactionLog;

use App\Contracts\AddToCartInterface;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\EffectTransactionInterface;
use App\Contracts\Policies\ValidatingReferralSistemInterface;
use App\Contracts\Policies\ProceedReferralSistemInterface;

class BalinAddToCart implements AddToCartInterface 
{
	protected $sale;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $pre_voucher;
	protected $pro;
	protected $pro_voucher;
	protected $post;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(ValidatingTransactionInterface $pre, ProceedTransactionInterface $pro, EffectTransactionInterface $post, ValidatingReferralSistemInterface $pre_voucher, ProceedReferralSistemInterface $pro_voucher)
	{
		$this->errors 		= new MessageBag;
		$this->pre 			= $pre;
		$this->pre_voucher 	= $pre_voucher;
		$this->pro 			= $pro;
		$this->pro_voucher 	= $pro_voucher;
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
		$customer 						= Customer::findorfail($this->sale['user_id']);
		$pre_sale 						= Sale::id($this->sale['id'])->first();
	
		if($pre_sale)
		{
			$sale_id 					= $pre_sale->id;
		}
		else
		{
			$sale_id 					= 0;
		}

		/** PREPROCESS */

		//1. Validate Previous Transaction
		$this->pre->validateprevioustransaction($sale_id, $customer); 

		//2. Validate Buyer
		$this->pre->validatebuyer($customer); 

		//3. Validate Stock, Price, Calculate Price main product
		if(isset($this->sale['transactiondetails']))
		{
			$this->pre->validatesaleitem($this->sale['transactiondetails']); 
		}

		//4. Generate unique number
		if($pre_sale)
		{
			$this->sale['unique_number']	= $this->pre->getuniquenumber($pre_sale); 
		}

		if(isset($this->sale['transactionextensions']))
		{
			//4. Validate Stock, Calculate Price of packing ornament
			$this->pre->validatepackingornament($this->sale['transactionextensions']); 
		}

		if(isset($this->sale['shipment']))
		{
			//5. Validate shipping cost, calculate shipping cost address
			$this->pre->validateshippingaddress($this->sale['shipment']); 
		}

		if(isset($this->sale['voucher']))
		{
			//6. Validate voucher
			$this->pre->validateshoppingvoucher((is_null($this->sale['voucher']) || !is_array($this->sale['voucher'])) ? [] : $this->sale['voucher']); 
		}

		if($this->pre->errors->count())
		{
			$this->errors 				= $this->pre->errors;

			return false;
		}

		$this->purchase['type']			= 'sell'; 

		//------- Area of validating entry promo referral -------//

		//7. Get referral code
		if(!isset($this->sale['voucher']['id']) && isset($this->sale['voucher']['code']) && !is_null($this->pre->voucher) && $this->pre->voucher->type == 'promo_referral')
		{
			$this->pre_voucher->validatedownline($customer); 

			if($this->pre_voucher->errors->count())
			{
				$this->errors 				= $this->pre_voucher->errors;

				return false;
			}

		}

		//----- End Area of validating entry promo referral -----//

		//8. set transact_at
		$this->sale['transact_at'] 		= \Carbon\Carbon::now()->format('Y-m-d H:i:s');

		//9. set shipping cost 
		$this->sale['shipping_cost']	= $this->pre->getshippingcost();

		//10. set voucher discount 
		$this->sale['voucher_discount']	= $this->pre->getvoucherdiscount();

		\DB::BeginTransaction();

		/** PROCESS */

		//11. Store Data Transaksi
		$this->pro->store($this->sale); 
		
		//12. Store sale item
		$this->pro->storesaleitem($this->pro->sale, isset($this->sale['transactiondetails']) ? $this->sale['transactiondetails'] : []); 

		//13. Store packing ornament
		if(isset($this->sale['transactionextensions']))
		{
			$this->pro->storepackingornament($this->pro->sale, $this->sale['transactionextensions']); 
		}

		//14. Store Shipping Address
		if(isset($this->sale['shipment']))
		{
			$this->pro->shippingaddress($this->pro->sale, $this->sale['shipment']); 
		}

		//------- Area of store entry promo referral -------//
		
		//15. reduce and add point if voucher -eq promo referral
		if(!isset($this->sale['voucher']['id']) && isset($this->sale['voucher']['code']) && !is_null($this->pre->voucher) && $this->pre->voucher->type == 'promo_referral')
		{
			$promo_referral 		= Voucher::code($this->sale['voucher']['code'])->type('promo_referral')->first();
			
			//15a. Store bonus for downline
			$this->pro_voucher->storebonusesvoucher($customer, $promo_referral); 

			//15b. requce upline quota
			$this->pro_voucher->storequotavoucher($promo_referral, $customer); 

			if($this->pro_voucher->errors->count())
			{
				\DB::rollback();

				$this->errors 		= $this->pro_voucher->errors;

				return false;
			}
		}		

		//----- End Area of store entry promo referral -----//

		//16. Store Log Transaksi
		$this->pro->updatestatus($this->pro->sale, 'cart'); 
		
		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//17. Return Sale Model Object
		$this->saved_data	= $this->pro->sale;

		return true;
	}
}
