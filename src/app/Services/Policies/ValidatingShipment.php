<?php

namespace App\Services\Policies;

use App\Entities\Shipment;

use App\Contracts\Policies\ValidatingShipmentInterface;

use Illuminate\Support\MessageBag;

class ValidatingShipment implements ValidatingShipmentInterface
{
	public $errors;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 	= new MessageBag;
	}

	public function validateshippingnotes(array $shipment)
	{
		if(empty($shipment['receipt_number']))
		{
			$this->errors->add('Shipment', 'Nomor resi pengiriman tidak boleh kosong');
		}

		$receipt_number 			= Shipment::receiptnumber($shipment['receipt_number'])->notid($shipment['id'])->first();

		if($receipt_number)
		{
			$this->errors->add('Shipment', 'Ada duplikasi nomor resi pengiriman');
		}
	}
}

