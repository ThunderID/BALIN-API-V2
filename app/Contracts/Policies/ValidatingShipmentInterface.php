<?php

namespace App\Contracts\Policies;

interface ValidatingShipmentInterface
{
	public function validateshippingnotes(array $shipment);
}
