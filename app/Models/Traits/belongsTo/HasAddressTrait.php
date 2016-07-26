<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs to address.
 *
 * @author cmooy
 */
trait HasAddressTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasAddressTraitConstructor()
	{
		//
	}
	
	/**
	 * check if model has address
	 *
	 **/
	public function Address()
	{
		return $this->belongsTo('App\Models\Address');
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