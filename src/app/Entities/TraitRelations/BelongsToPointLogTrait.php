<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models belongs to PointLog.
 *
 * @author cmooy
 */
trait BelongsToPointLogTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function BelongsToPointLogTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function PointLog()
	{
		return $this->belongsTo('App\Entities\PointLog', 'point_log_id');
	}
}