<?php

namespace App\Services\Policies;

use App\Entities\Courier;
use App\Entities\Shipment;

use App\Contracts\Policies\ValidatingExpeditionInterface;

use Illuminate\Support\MessageBag;

class ValidatingExpedition implements ValidatingExpeditionInterface
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

	public function validatecourier(array $courier)
	{
		//
	}

	public function validateshippingcost(array $shippingcost)
	{
		//
	}

	public function validateaddress(array $address)
	{
		//
	}

	public function validatedeletecourier(Courier $courier)
	{
		$used_courier 		= Shipment::courierid($courier['id'])->count();

		if($used_courier)
		{
			$this->errors->add('Expedition', 'Tidak dapat menghapus Kurir yang pernah digunakan');
		}
	}

	public function validatedeleteshippingcost(Courier $courier)
	{
		//
	}
}

