<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Prices.
 *
 * @author cmooy
 */
trait HasPricesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPricesTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Prices()
	{
		return $this->hasMany('App\Models\Price');
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