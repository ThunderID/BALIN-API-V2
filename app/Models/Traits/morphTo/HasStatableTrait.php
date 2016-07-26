<?php namespace App\Models\Traits\morphTo;

/**
 * Trait for models morph to Stat.
 *
 * @author cmooy
 */
trait HasStatableTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasStatableTraitConstructor()
	{
		//
	}

	/**
	 * define morph to as Statable
	 *
	 **/
    public function Statable()
    {
        return $this->morphTo();
    }

	/**
	 * find Statable id
	 *
	 **/
    public function scopeStatableID($query, $variable)
    {
		return $query->where('statable_id', $variable);
    }

	/**
	 * find Statable type
	 *
	 **/
    public function scopeStatableType($query, $variable)
    {
    	if(is_array($variable))
    	{
			return $query->whereIn('statable_type', $variable);
    	}

		return $query->where('statable_type', $variable);
    }
}