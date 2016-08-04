<?php

namespace App\Contracts\Policies;

use App\Entities\Sale;

interface EffectShipmentInterface
{
	public function sendmailshippingpackage(Sale $sale);
}
