<?php 

namespace App\Entities\TraitLibraries;

/**
 * available function who hath relationship with transactions' status
 *
 * @author cmooy
 */
trait JoinShipmentTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function JoinShipmentTraitConstructor()
	{
		//
	}

	/**
	 * joining shipment from transaction
	 *
	 **/
	public function scopeJoinAddressFromShipment($query, $variable)
	{
		return $query
		->join('addresses', function ($join) use($variable) 
		 {
			$join->on ( 'addresses.id', '=', 'shipments.address_id' )
			->wherenull('addresses.deleted_at')
			;
		})
		;
	}
}