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
}