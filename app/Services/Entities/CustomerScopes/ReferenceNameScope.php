<?php 

namespace App\Services\Entities\CustomerScopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use DB;

/**
 * Scope to get code referral
 *
 * @return points
 * @author cmooy
 */
class ReferenceNameScope implements ScopeInterface  
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
		$builder->selectraw('IFNULL(users2.name, IFNULL(vouchers2.code,"EMPTY")) as reference_name')
				->leftjoin('point_logs', function($join)
				{
					$join->on('point_logs.user_id', '=', 'users.id')
					->whereIn('point_logs.reference_type', ['App\Entities\User', 'App\Entities\Voucher', 'App\Models\User', 'App\Models\Voucher'])
					->wherenull('point_logs.deleted_at')
					;
				})
				->leftjoin(DB::raw('(SELECT name, id, deleted_at from users) as users2'), function ($join)
				{
					$join->on('users2.id', '=', 'point_logs.reference_id')
					->whereIn('point_logs.reference_type', ['App\Entities\User', 'App\Models\User'])
					->wherenull('users2.deleted_at')
					;
				})
				->leftjoin(DB::raw('(SELECT code, id, deleted_at from tmp_vouchers) as vouchers2'), function ($join)
				{
					$join->on('vouchers2.id', '=', 'point_logs.reference_id')
					->whereIn('point_logs.reference_type', ['App\Entities\Voucher', 'App\Models\Voucher'])
					->wherenull('vouchers2.deleted_at')
					;
				})
				;
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
