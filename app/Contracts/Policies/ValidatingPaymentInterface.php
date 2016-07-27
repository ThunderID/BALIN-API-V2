<?php

namespace App\Contracts\Policies;

use App\Entities\Sale;

interface ValidatingPaymentInterface
{
	public function validatebillshaventpaid(Sale $sale);

	public function validatepaymentamount(Sale $sale, array $payment);
}
