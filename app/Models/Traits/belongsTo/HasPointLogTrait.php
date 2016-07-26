<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models has many Point Logs.
 *
 * @author cmooy
 */
trait HasPointLogTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPointLogTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function PointLog()
	{
		return $this->belongsTo('App\Models\PointLog', 'point_log_id');
	}
}