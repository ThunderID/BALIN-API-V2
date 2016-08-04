<?php 

namespace App\Entities\TraitLibraries;

/**
 * available function who hath name trait
 *
 * @author cmooy
 */
trait FieldSlugTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function FieldSlugTraitConstructor()
	{
		//
	}

	/**
	 * scope to get condition where Slug
	 *
	 * @param string or array of entity' Slug
	 **/
	public function scopeSlug($query, $variable)
	{
		return 	$query->where('slug', $variable);
	}
}