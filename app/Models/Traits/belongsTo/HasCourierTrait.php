<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs to courier.
 *
 * @author cmooy
 */
trait HasCourierTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasCourierTraitConstructor()
	{
		//
	}


	/**
	 * call belongs to relationship
	 *
	 **/
	public function Courier()
	{
		return $this->belongsTo('App\Models\Courier');
	}

	/**
	 * check if model has courier
	 *
	 **/
	public function scopeHasCourier($query, $variable)
	{
		return $query->whereHas('courier', function($q)use($variable){$q;});
	}

	/**
	 * check if model has courier in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeCourierID($query, $variable)
	{
		return $query->where('courier_id', $variable);
	}

	/**
	 * check if model has courier in certain name
	 *
	 * @var string name
	 **/
	public function scopeCourierName($query, $variable)
	{
		return $query->whereHas('courier', function($q)use($variable){$q->name($variable);});
	}
}