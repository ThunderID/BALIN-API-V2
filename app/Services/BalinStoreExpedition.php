<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\StoreExpeditionInterface;

use App\Contracts\Policies\ValidatingExpeditionInterface;
use App\Contracts\Policies\ProceedExpeditionInterface;
use App\Contracts\Policies\EffectExpeditionInterface;

use App\Entities\Courier as Expedition;

class BalinStoreExpedition implements StoreExpeditionInterface 
{
	protected $courier;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $post;
	protected $pro;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(ValidatingExpeditionInterface $pre, ProceedExpeditionInterface $pro, EffectExpeditionInterface $post)
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
	public function fill(array $courier)
	{
		$this->courier 		= $courier;
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
		/** PREPROCESS */

		//1. Validate Expedition
		$this->pre->validatecourier($this->courier); 

		//2. Validateshippingcost
		if(isset($this->courier['shippingcosts']))
		{
			$this->pre->validateshippingcost($this->courier['shippingcosts']); 
		}

		//3. Validate address
		if(isset($this->courier['addresses']))
		{
			$this->pre->validateaddress($this->courier['addresses']); 
		}

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//4. store Expedition
		$this->pro->storecourier($this->courier); 

		//5. store shipping cost
		if(isset($this->courier['shippingcosts']))
		{
			$this->pro->storeshippingcost($this->pro->courier, $this->courier['shippingcosts']); 
		}

		//6. store addresses
		if(isset($this->courier['addresses']))
		{
			$this->pro->storeaddress($this->pro->courier, $this->courier['addresses']); 
		}

		//7. store images
		$this->pro->storeimage($this->pro->courier, $this->courier['images']); 

		if($this->pro->errors->count())
		{
			\DB::rollback();
			
			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//8. Return Expedition Model Object
		$this->saved_data	= $this->pro->courier;

		return true;
	}
}
