<?php

namespace App\Contracts\Policies;

use App\Entities\Courier;

interface ValidatingExpeditionInterface
{
	public function validatecourier(array $courier);

	public function validateshippingcost(array $shippingcost);

	public function validateaddress(array $address);

	public function validatedeletecourier(Courier $courier);
	
	public function validatedeleteshippingcost(Courier $courier);
}
