<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models belongs to address.
 *
 * @author cmooy
 */
trait BelongsToAddressTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function BelongsToAddressTraitConstructor()
	{
		//
	}
	
	/**
	 * check if model has address
	 *
	 **/
	public function Address()
	{
		return $this->belongsTo('App\Entities\Address');
	}

	/**
	 * check if model has address in certain id
	 *
	 * @var singular id
	 **/
	public function scopeAddressID($query, $variable)
	{
		return $query->where('address_id', $variable);
	}

}