<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Models\User;
use App\Entities\Sale;
use App\Models\TransactionLog;

use App\Contracts\CancelOrderInterface;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\EffectTransactionInterface;

use App\Services\Policies\ValidatingPayment;

class BalinCancelOrder implements CancelOrderInterface 
{
	protected $sale;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $pre_pay;
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
		$this->pre_pay	= new ValidatingPayment;
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
		$sale 							= Sale::findornew($this->sale['id']);

		/** PREPROCESS */

		//1. Validate Buyer
		$this->pre_pay->validatebillshaventpaid($sale); 

		\DB::BeginTransaction();

		/** PROCESS */

		//2. rollback point
		$this->pro->revertbalinpoint($sale); 
		
		//3. set status cancel
		$this->pro->updatestatus($sale, 'canceled');

		if($this->pro->errors->count())
		{
			\DB::rollback();

			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::Commit();

		/** POST PROCESS */

		//4. Send Mail
		$this->post->sendmailcancelorder($this->pro->sale, $this->sale['client_id']);

		//5. Return Sale Model Object
		$this->saved_data	= $this->pro->sale;

		return true;
	}
}
