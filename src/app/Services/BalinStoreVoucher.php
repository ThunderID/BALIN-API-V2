<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\StoreVoucherInterface;

use App\Contracts\Policies\ValidatingVoucherInterface;
use App\Contracts\Policies\ProceedVoucherInterface;
use App\Contracts\Policies\EffectVoucherInterface;

use App\Entities\Voucher;

class BalinStoreVoucher implements StoreVoucherInterface 
{
	protected $voucher;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $post;
	protected $pro;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(ValidatingVoucherInterface $pre, ProceedVoucherInterface $pro, EffectVoucherInterface $post)
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
	public function fill(array $voucher)
	{
		$this->voucher 		= $voucher;
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

		//1. Validate Voucher
		$this->pre->validatevoucher($this->voucher); 

		//2. Get Quota
		$this->voucher['quota'] = $this->pre->getquota($this->voucher); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//3. store Voucher
		$this->pro->storevoucher($this->voucher); 

		//4. store quota
		$this->pro->storequota($this->pro->voucher, $this->voucher['quota']); 

		if($this->pro->errors->count())
		{
			\DB::rollback();
			
			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//5. Return Voucher Model Object
		$this->saved_data	= $this->pro->voucher;

		return true;
	}
}
