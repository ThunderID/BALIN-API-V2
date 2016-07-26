<?php namespace App\Models\Traits;

/**
 * available function to get name of records
 *
 * @author cmooy
 */
trait HasSlugTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasSlugTraitConstructor()
	{
		//
	}

	/**
	 * scope to get condition where slug
	 *
	 * @param slug
	 **/
	public function scopeSlug($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn($query->getModel()->table.'.slug', $variable);
		}

		return 	$query->where($query->getModel()->table.'.slug', $variable);
	}
}