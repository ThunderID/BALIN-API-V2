<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for Entities has many varians.
 *
 * @author cmooy
 */

trait HasManyPricesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasManyPricesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Prices()
	{
		return $this->hasMany('App\Entities\Price');
	}

	/**
	 * check if model has price
	 *
	 **/
	public function scopeHasPrices($query, $variable)
	{
		return $query->whereHas('prices', function($q)use($variable){$q;});
	}

	/**
	 * check if model has price in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopePriceID($query, $variable)
	{
		return $query->whereHas('prices', function($q)use($variable){$q->id($variable);});
	}

	/**
	 * check if model has discount now
	 *
	 * @var none
	 **/
	public function scopeDiscount($query, $variable)
	{
		return $query->where('promo_price', '>', '0');
	}
}