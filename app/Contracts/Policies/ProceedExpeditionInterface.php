<?php

namespace App\Contracts\Policies;

use App\Entities\Courier;

interface ProceedExpeditionInterface
{
	public function storecourier(array $courier);

	public function storeaddress(Courier $courier, array $addresses);
	
	public function storeshippingcost(Courier $courier, array $shippingcosts);

	public function storeimage(Courier $courier, array $images);

	public function deletecourier(Courier $courier);

	public function deleteshippingcost(Courier $courier);
}
