<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for Entities has many varians.
 *
 * @author cmooy
 */

trait BelongsToManyProductsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function BelongsToManyProductsTraitConstructor()
	{
		//
	}

	/**
	 * call belongsto many relationship products
	 *
	 **/
	public function Products()
	{
		return $this->belongsToMany('App\Entities\Product', 'categories_products', 'category_id');
	}

	/**
	 * call belongsto many relationship products
	 *
	 **/
	public function ReadableProducts()
	{
		return $this->belongsToMany('App\Entities\ReadableProduct', 'categories_products', 'category_id', 'product_id');
	}

	/**
	 * check if model has category in certain slug
	 *
	 * @var array or singular slug
	 **/
	public function scopeProductCategoriesSlug($query, $variable)
	{
		return $query->whereHas('readableproducts', function($q)use($variable){$q->categoriesslug($variable);});
	}

}