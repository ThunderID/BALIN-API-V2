<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models morph to Statable.
 *
 * @author cmooy
 */
trait MorphToStatableTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function MorphToStatableTraitConstructor()
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