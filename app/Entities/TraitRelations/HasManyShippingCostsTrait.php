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
	 * call has many relationship
	 *
	 **/
	public function ShippingCosts()
	{
		return $this->hasMany('App\Entities\ShippingCost');
	}
}