<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\DeleteVoucherInterface;

use App\Contracts\Policies\ValidatingVoucherInterface;
use App\Contracts\Policies\ProceedVoucherInterface;
use App\Contracts\Policies\EffectVoucherInterface;

use App\Entities\Voucher;

class BalinDeleteVoucher implements DeleteVoucherInterface 
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
	 * Save
	 *
	 * Here's the workflow
	 * 
	 * @return Response
	 */
	public function delete(Voucher $voucher)
	{
		$this->voucher 			= $voucher->toArray();
		
		/** PREPROCESS */

		//1. Validate Voucher
		$this->pre->validatedeletevoucher($voucher); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//2. Delete quotas
		$this->pro->deletequota($voucher); 

		//3. Delete vouchers
		$this->pro->deletevoucher($voucher); 

		if($this->pro->errors->count())
		{
			\DB::rollback();
			
			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//4. Return Voucher Model Object
		$this->saved_data	= $this->voucher;

		return true;
	}
}
