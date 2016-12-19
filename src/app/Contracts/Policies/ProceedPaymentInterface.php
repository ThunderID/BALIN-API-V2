<?php

namespace App\Contracts\Policies;

use App\Entities\Sale;

interface ProceedPaymentInterface
{
	public function storepayment(Sale $sale, array $payment);
}
