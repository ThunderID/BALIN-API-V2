<?php

namespace App\Contracts\Policies;

use App\Entities\Sale;

interface EffectTransactionInterface
{
	public function sendmailinvoice(Sale $sale, $client_id);
	
	public function sendmailpaymentacceptance(Sale $sale, $client_id);
}
