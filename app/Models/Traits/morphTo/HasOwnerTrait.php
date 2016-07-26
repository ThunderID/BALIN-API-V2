<?php namespace App\Models\Traits\morphTo;

/**
 * Trait for models morph to image.
 *
 * @author cmooy
 */
trait HasOwnerTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasOwnerTraitConstructor()
	{
		//
	}

	/**
	 * define morph to as Owner
	 *
	 **/
    public function Owner()
    {
        return $this->morphTo();
    }

	/**
	 * find Owner id
	 *
	 **/
    public function scopeOwnerID($query, $variable)
    {
		return $query->where('owner_id', $variable);
    }

	/**
	 * find Owner type
	 *
	 **/
    public function scopeOwnerType($query, $variable)
    {
		return $query->where('owner_type', $variable);
    }
}