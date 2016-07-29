<?php

namespace App\Services\Policies;

use App\Entities\Sale;
use App\Entities\Payment;

use Illuminate\Support\MessageBag;

use App\Contracts\Policies\ProceedPaymentInterface;

class ProceedPayment implements ProceedPaymentInterface
{
	public $errors;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 		= new MessageBag;
	}

	public function storepayment(Sale $sale, array $payment)
	{
		$stored_payment					= Payment::findornew($payment['id']);
		
		$stored_payment->fill($payment);
		$stored_payment->transaction_id = $sale->id;

		if(!$stored_payment->save())
		{
			$this->errors->add('Payment', $stored_payment->getError());
		}
	}
}
