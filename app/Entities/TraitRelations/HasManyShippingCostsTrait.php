<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models has many shippingcost.
 *
 * @author cmooy
 */
trait HasManyShippingCostsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasManyShippingCostsTraitConstructor()
	{
		//
	}
	
	/**
	 * call has many relationship
	 *
	 **/
	public function ShippingCosts()
	{
		return $this->hasMany('App\Entities\ShippingCost');
	}
}