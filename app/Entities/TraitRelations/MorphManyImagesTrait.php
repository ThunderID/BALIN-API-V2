<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for Entities has many varians.
 *
 * @author cmooy
 */

trait MorphManyImagesTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function MorphManyImagesTraitConstructor()
	{
		//
	}

	/**
	 * call morph many relationship
	 *
	 **/
	public function Images()
	{
		return $this->morphMany('App\Entities\Image', 'imageable')->orderby('created_at','desc');
	}
}