<?php 

namespace App\Services\Entities\CustomerScopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Scope to count total point
 *
 * @return points
 * @author cmooy
 */
class TotalPointScope implements ScopeInterface  
{
	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return void
	 */
	public function apply(Builder $builder, Model $model)
	{
		$builder->selectraw('(SELECT IFNULL(SUM(point_logs3.amount), 0) from point_logs as point_logs3 where point_logs3.user_id = users.id and point_logs3.expired_at >= CONVERT_TZ(NOW(),"+00:00","+07:00") and point_logs3.deleted_at is null) as total_point');
	}

	/**
	 * Remove the scope from the given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return void
	 */
	public function remove(Builder $builder, Model $model)
	{
	    $query = $builder->getQuery();
	    unset($query);
	}
}
