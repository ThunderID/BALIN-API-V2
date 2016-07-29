<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models has one shipment.
 *
 * @author cmooy
 */
trait HasOneShipmentTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasOneShipmentTraitConstructor()
	{
		//
	}

	/**
	 * call has one relationship
	 *
	 **/
	public function Shipment()
	{
		return $this->hasOne('App\Entities\Shipment', 'transaction_id');
	}
}