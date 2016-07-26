<?php namespace App\Models\Traits\morphTo;

/**
 * Trait for models morph to image.
 *
 * @author cmooy
 */
trait HasImageableTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasImageableTraitConstructor()
	{
		//
	}

	/**
	 * define morph to as imageable
	 *
	 **/
    public function imageable()
    {
        return $this->morphTo();
    }

	/**
	 * find imageable id
	 *
	 **/
    public function scopeImageableID($query, $variable)
    {
		return $query->where('imageable_id', $variable);
    }

	/**
	 * find imageable type
	 *
	 **/
    public function scopeImageableType($query, $variable)
    {
		return $query->where('imageable_type', $variable);
    }
}