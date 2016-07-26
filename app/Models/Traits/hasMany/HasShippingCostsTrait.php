<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many ShippingCosts.
 *
 * @author cmooy
 */
trait HasShippingCostsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasShippingCostsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function ShippingCosts()
	{
		return $this->hasMany('App\Models\ShippingCost');
	}
}