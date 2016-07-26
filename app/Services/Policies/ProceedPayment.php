<?php

namespace App\Services\Policies;

use App\Entities\Sale;

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
}
