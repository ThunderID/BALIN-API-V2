<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models has many shippingcost.
 *
 * @author cmooy
 */
trait HasManyAddressesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasManyAddressesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Addresses()
	{
		return $this->hasMany('App\Entities\Address', 'owner_id')->where('owner_type', get_class($this));
	}

	/**
	 * check if model has Address
	 *
	 **/
	public function scopeHasAddresses($query, $variable)
	{
		return $query->whereHas('addresses', function($q)use($variable){$q;});
	}

	/**
	 * check if model has Address in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeAddressID($query, $variable)
	{
		return $query->whereHas('addresses', function($q)use($variable){$q->id($variable);});
	}
}