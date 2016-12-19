<?php 

namespace App\Entities\TraitLibraries;

/**
 * available function to get result of price
 *
 * @author cmooy
 */
trait SelectPriceTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function SelectPriceTraitConstructor()
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