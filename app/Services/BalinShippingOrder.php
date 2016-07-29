<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Entities\Sale;

use App\Contracts\ShippingOrderInterface;

use App\Contracts\Policies\ValidatingShipmentInterface;
use App\Contracts\Policies\ProceedShipmentInterface;
use App\Contracts\Policies\EffectShipmentInterface;

use App\Services\Policies\ValidatingTransaction;
use App\Services\Policies\ValidatingPayment;
use App\Services\Policies\ProceedTransaction;

class BalinShippingOrder implements ShippingOrderInterface 
{
	protected $sale;
	protected $errors;
	protected $saved_data;
	protected $pre_sale;
	protected $pre;
	protected $pre_pay;
	protected $pro_sale;
	protected $pro;
	protected $post;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(ValidatingShipmentInterface $pre, ProceedShipmentInterface $pro, EffectShipmentInterface $post)
	{
		$this->errors 	= new MessageBag;
		$this->pre 		= $pre;
		$this->pre_sale	= new ValidatingTransaction;
		$this->pre_pay	= new ValidatingPayment;
		$this->pro 		= $pro;
		$this->pro_sale = new ProceedTransaction;
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
		$this->pre_sale->validateshippingaddress($sale['shipment']->toArray()); 

		if($this->pre_sale->errors->count())
		{
			$this->errors 		= $this->pre_sale->errors;

			return false;
		}

		//3. validate receipt number
		$this->pre->validateshippingnotes($this->sale['shipment']); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}
		
		\DB::BeginTransaction();

		/** PROCESS */

		//4. update receipt number
		$this->pro->updateshippingnotes($sale, $this->sale['shipment']);

		//5. update status shipping
		$this->pro_sale->updatestatus($sale, 'shipping');

		if($this->pro_sale->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro_sale->errors;

			return false;
		}

		\DB::Commit();

		//6. kirim email shipping notes
		$this->post->sendmailshippingpackage($this->pro_sale->sale, $this->sale['client_id']);

		//7. Return Sale Model Object
		$this->saved_data	= $this->pro_sale->sale;

		return true;
	}
}
