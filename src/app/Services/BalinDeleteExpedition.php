<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\DeleteExpeditionInterface;

use App\Contracts\Policies\ValidatingExpeditionInterface;
use App\Contracts\Policies\ProceedExpeditionInterface;
use App\Contracts\Policies\EffectExpeditionInterface;

use App\Entities\Courier;

class BalinDeleteExpedition implements DeleteExpeditionInterface 
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
	 * Save
	 *
	 * Here's the workflow
	 * 
	 * @return Response
	 */
	public function delete(Courier $courier)
	{
		$this->courier 			= $courier->toArray();
		
		/** PREPROCESS */

		//1. Validate Courier
		$this->pre->validatedeletecourier($courier); 
		
		//2. Validate shipping cost
		$this->pre->validatedeleteshippingcost($courier); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//3. Delete courier
		$this->pro->deletecourier($courier); 

		//4. Delete shipping cost
		$this->pro->deleteshippingcost($courier); 

		//5. Delete Image
		$this->pro->deleteimage($courier); 

		if($this->pro->errors->count())
		{
			\DB::rollback();
			
			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//6. Return Expedition Model Object
		$this->saved_data	= $this->courier;

		return true;
	}
}
