<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Addresses.
 *
 * @author cmooy
 */
trait HasAddressesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasAddressesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Addresses()
	{
		return $this->hasMany('App\Models\Address', 'owner_id')->where('owner_type', get_class($this));
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