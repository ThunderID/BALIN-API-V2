<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs to product.
 *
 * @author cmooy
 */
trait HasProductTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasProductTraitConstructor()
	{
		//
	}

	/**
	 * call belongsto relationship with product
	 *
	 **/
	public function Product()
	{
		return $this->belongsTo('App\Models\Product');
	}

	/**
	 * check if model has product
	 *
	 **/
	public function scopeHasProduct($query, $variable)
	{
		return $query->whereHas('product', function($q)use($variable){$q;});
	}

	/**
	 * check if model has product in certain id
	 *
	 * @var singular id
	 **/
	public function scopeProductID($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn('product_id', $variable);
		}

		return $query->where('product_id', $variable);
	}

	/**
	 * check if model has product in certain name
	 *
	 * @var singular name
	 **/
	public function scopeProductName($query, $variable)
	{
		return $query
		 ->join('products', function ($join) use($variable) 
			 {
                                    $join->on ( $this->getTable().'.product_id', '=', 'products.id' )
                                    ->where('products.name', 'like', '%'.$variable.'%')
                                    ->wherenull('products.deleted_at')
                                    ;
			})
		;
	}
}