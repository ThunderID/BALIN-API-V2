<?php

namespace App\Contracts\Policies;

use App\Entities\Sale;

interface EffectTransactionInterface
{
	public function sendmailinvoice(Sale $sale);
	
	public function sendmailpaymentacceptance(Sale $sale);

	public function sendmailcancelorder(Sale $sale);

	public function sendmaildeliveredorder(Sale $sale);
}
