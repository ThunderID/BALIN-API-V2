<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for Entities has One image.
 *
 * @author cmooy
 */

trait HasOneImageTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasOneImageTraitConstructor()
	{
		//
	}

	/**
	 * call has one relationship
	 *
	 **/
	public function Image()
	{
		return $this->hasOne('App\Entities\Image', 'imageable_id')->wherein('imageable_type', ['App\Models\StoreSetting', 'App\Models\Slider', 'App\Entities\StoreSetting', 'App\Entities\Slider']);
	}
}