<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for Entities has many varians.
 *
 * @author cmooy
 */

trait HasManyProductLabelsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasManyProductLabelsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Labels()
	{
		return $this->hasMany('App\Entities\ProductLabel');
	}

	/**
	 * check if model has Label in certain name
	 * @var string name
	 *
	 **/
	public function scopeLabelsName($query, $variable)
	{
		return $query->wherehas('labels', function($q)use($variable){$q->name($variable);});
	}
}