<?php namespace App\Models\Traits\hasMany;

use Illuminate\Support\Pluralizer;

/**
 * Trait for models has many Point Logs.
 *
 * @author cmooy
 */
trait HasPointLogsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPointLogsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function PointLogs()
	{
		return $this->hasMany('App\Models\PointLog', Pluralizer::singular($this->getTable()).'_id');
	}

	/**
	 * check if model has point log
	 *
	 **/
	public function scopeHasPointLogs($query, $variable)
	{
		return $query->whereHas('pointlogs', function($q)use($variable){$q;});
	}

	/**
	 * check if model has point log in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopePointLogID($query, $variable)
	{
		return $query->whereHas('pointlogs', function($q)use($variable){$q->id($variable);});
	}

	/**
	 * call has many relationship in term of reference were users
	 *
	 **/
	public function MyReferrals()
	{
		return $this->hasMany('App\Models\PointLog', 'reference_id')->whereIn('reference_type', ['App\Models\User', 'App\Models\Admin', 'App\Models\Customer']);
	}

	/**
	 * call has many relationship in term of used for paid sales
	 *
	 **/
	public function PaidPointLogs()
	{
		return $this->hasMany('App\Models\PointLog', 'reference_id')->where('reference_type', '=', 'App\Models\Sale')->where('amount', '<', 0);
	}

	/**
	 * scope to find point doesnt havent get cut
	 *
	 * @param user id
	 */
	public function scopeHaventGetCut($query, $variable)
	{
		return 	$query
						->selectraw('point_logs.*')
						->selectraw("SUM(IFNULL((SELECT sum(amount) FROM point_logs as point_logs2 WHERE point_logs2.point_log_id = point_logs.id and point_logs2.deleted_at is null),0) + point_logs.amount) as amount")
						->havingraw("SUM(IFNULL((SELECT sum(amount) FROM point_logs as point_logs2 WHERE point_logs2.point_log_id = point_logs.id and point_logs2.deleted_at is null),0) + point_logs.amount) > 0")
						->groupby('point_logs.user_id')
		;
	}
}