<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many varians.
 *
 * @author cmooy
 */

trait HasVariansTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasVariansTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Varians()
	{
		return $this->hasMany('App\Models\Varian');
	}

	/**
	 * check if model has varian in size
	 *
	 * @var array or singular size
	 **/
	public function scopeVarianSize($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn('varians.size', $variable);
		}

		return $query->where('varians.size', $variable);
	}
}