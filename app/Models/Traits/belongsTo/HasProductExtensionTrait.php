<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs to product extension.
 *
 * @author cmooy
 */
trait HasProductExtensionTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasProductExtensionTraitConstructor()
	{
		//
	}

	/**
	 * call belongsto relationship with productExtension
	 *
	 **/
	public function ProductExtension()
	{
		return $this->belongsTo('App\Models\ProductExtension');
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