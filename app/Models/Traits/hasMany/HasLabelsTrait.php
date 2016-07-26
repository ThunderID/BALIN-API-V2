<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Labels.
 *
 * @author cmooy
 */
trait HasLabelsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasLabelsTraitConstructor()
	{
		//
	}
	
	/**
	 * call has many relationship
	 *
	 **/
	public function Labels()
	{
		return $this->hasMany('App\Models\ProductLabel');
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