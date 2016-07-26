<?php namespace App\Models\Traits\belongsToMany;

/**
 * Trait for models belongs to many Products.
 *
 * @author cmooy
 */
trait HasProductsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasProductsTraitConstructor()
	{
		//
	}

	/**
	 * call belongsto many relationship products
	 *
	 **/
	public function Products()
	{
		return $this->belongsToMany('App\Models\Product', 'categories_products', 'category_id');
	}
}