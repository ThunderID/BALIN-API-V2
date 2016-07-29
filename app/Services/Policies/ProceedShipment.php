<?php

namespace App\Services\Policies;

use App\Entities\Sale;
use App\Entities\Shipment;

use App\Contracts\Policies\ProceedShipmentInterface;

use Illuminate\Support\MessageBag;

class ProceedShipment implements ProceedShipmentInterface
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

	public function updateshippingnotes(Sale $sale, array $shipment)
	{
		$stored_shipment	= Shipment::id($shipment['id'])->transactionid($sale['id'])->first();

		$stored_shipment->fill($shipment);
		$stored_shipment->transaction_id = $sale->id;

		if(!$stored_shipment->save())
		{
			$this->errors->add('shipment', $stored_shipment->getError());
		}
	}
}

