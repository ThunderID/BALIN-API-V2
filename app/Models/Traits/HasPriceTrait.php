<?php namespace App\Models\Traits;

/**
 * available function to get result of price
 *
 * @author cmooy
 */
trait HasPriceTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPriceTraitConstructor()
	{
		//
	}

	/**
	 * check if price not null
	 *
	 * @return cart_item
	 **/
	public function scopeHavingPrice($query, $variable)
	{
		return $query->wherenotnull('prices.price');
	}
}