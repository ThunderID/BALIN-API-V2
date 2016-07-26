<?php namespace App\Models\Traits\morphMany;

trait HasPointLogsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasPointLogsTraitConstructor()
	{
		//
	}

	public function PointLogs()
	{
		return $this->morphMany('App\Models\PointLog', 'reference');
	}
}