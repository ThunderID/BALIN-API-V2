<?php namespace App\Models\Traits\hasOne;

/**
 * Trait for models has one Image.
 *
 * @author cmooy
 */
trait HasImageTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasImageTraitConstructor()
	{
		//
	}
	
	/**
	 * call has one relationship
	 *
	 **/
	public function Image()
	{
		return $this->hasOne('App\Models\Image', 'imageable_id')->wherein('imageable_type', ['App\Models\StoreSetting', 'App\Models\Slider']);
	}
}