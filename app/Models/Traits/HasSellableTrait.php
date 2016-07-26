<?php namespace App\Models\Traits;

/**
 * available function to get result of Sellable 
 *
 * @author cmooy
 */
trait HasSellableTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasSellableTraitConstructor()
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