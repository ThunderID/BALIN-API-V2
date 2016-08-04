<?php 

namespace App\Entities\TraitLibraries;

/**
 * available function to get result of Sellable 
 *
 * @author cmooy
 */
trait SellableTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function SellableTraitConstructor()
	{
		//
	}

	/**
	 * business policy of sellable product
	 *
	 * @return cart_item
	 **/
	public function scopeSellable($query, $variable)
	{
		return $query->HavingCurrentStock(1)->HavingPrice(true);
		;
	}
}