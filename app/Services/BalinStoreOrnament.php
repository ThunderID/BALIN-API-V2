<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\StoreOrnamentInterface;

use App\Contracts\Policies\ValidatingOrnamentInterface;
use App\Contracts\Policies\ProceedOrnamentInterface;
use App\Contracts\Policies\EffectOrnamentInterface;

use App\Entities\ProductExtension as Ornament;

class BalinStoreOrnament implements StoreOrnamentInterface 
{
	protected $ornament;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $post;
	protected $pro;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(ValidatingOrnamentInterface $pre, ProceedOrnamentInterface $pro, EffectOrnamentInterface $post)
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
	public function fill(array $ornament)
	{
		$this->Ornament 		= $ornament;
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

		//1. Validate Ornament
		$this->pre->validateornament($this->Ornament); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//2. store Ornament
		$this->pro->storeornament($this->Ornament); 

		if($this->pro->errors->count())
		{
			\DB::rollback();
			
			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//3. Return Ornament Model Object
		$this->saved_data	= $this->pro->ornament;

		return true;
	}
}
