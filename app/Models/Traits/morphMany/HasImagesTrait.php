<?php namespace App\Models\Traits\morphMany;

/**
 * Trait for models morph many Images.
 *
 * @author cmooy
 */
trait HasImagesTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasImagesTraitConstructor()
	{
		//
	}
	
	/**
	 * call morph many relationship
	 *
	 **/
	public function Images()
	{
		return $this->morphMany('App\Models\Image', 'imageable')->orderby('created_at','desc');
	}
}