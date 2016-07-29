<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models belongs to user.
 *
 * @author cmooy
 */
trait BelongsToProductExtensionTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function BelongsToProductExtensionTraitConstructor()
	{
		//
	}

	/**
	 * call belongsto relationship with productExtension
	 *
	 **/
	public function ProductExtension()
	{
		return $this->belongsTo('App\Entities\ProductExtension');
	}

	/**
	 * check if model has productExtension
	 *
	 **/
	public function scopeHasProductExtension($query, $variable)
	{
		return $query->whereHas('productextension', function($q)use($variable){$q;});
	}

	/**
	 * check if model has productExtension in certain id
	 *
	 * @var singular id
	 **/
	public function scopeProductExtensionID($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn('product_extension_id', $variable);
		}

		return $query->where('product_extension_id', $variable);
	}
}