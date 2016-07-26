<?php namespace App\Models\Traits\hasOne;

/**
 * Trait for models has one shipment.
 *
 * @author cmooy
 */
trait HasShipmentTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasShipmentTraitConstructor()
	{
		//
	}

	/**
	 * call has one relationship
	 *
	 **/
	public function Shipment()
	{
		return $this->hasOne('App\Models\Shipment', 'transaction_id');
	}
}