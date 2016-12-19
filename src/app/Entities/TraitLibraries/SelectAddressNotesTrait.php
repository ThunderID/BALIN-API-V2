<?php 

namespace App\Entities\TraitLibraries;

/**
 * available function who hath relationship with transactions' status
 *
 * @author cmooy
 */
trait SelectAddressNotesTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function SelectAddressNotesTraitConstructor()
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