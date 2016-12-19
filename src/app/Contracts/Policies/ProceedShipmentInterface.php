<?php

namespace App\Contracts\Policies;

use App\Entities\Sale;

interface ProceedShipmentInterface
{
	public function updateshippingnotes(Sale $sale, array $shipment);
}
