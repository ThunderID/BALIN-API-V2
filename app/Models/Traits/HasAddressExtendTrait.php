<?php namespace App\Models\Traits;

use DB;

/**
 * available function who hath relationship with transactions' status
 *
 * @author cmooy
 */
trait HasAddressExtendTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasAddressExtendTraitConstructor()
	{
		//
	}

	/**
	 * get address notes
	 *
	 **/
	public function scopeAddressNotes($query, $variable)
	{
		return $query->selectraw('CONCAT_WS("", CONCAT_WS(" (", addresses.address, addresses.zipcode), ") ") as address_notes')
					->selectraw('addresses.phone as phone_notes')
					->JoinShipmentFromTransaction(true)
					->JoinAddressFromShipment(true)
					;
	}

	/**
	 * get shipping notes
	 *
	 **/
	public function scopeShippingNotes($query, $variable)
	{
		return $query->selectraw('shipments.receipt_number as shipping_notes')
					->JoinShipmentFromTransaction(true)
					;
	}
}