<?php 

namespace App\Services\Entities\VoucherScopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Scope to get total downline
 *
 * @return points
 * @author cmooy
 */
class QuotaScope implements ScopeInterface  
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
		$builder->selectraw('IFNULL(sum(quota_logs.amount),0) as quota')
						->leftjoin('quota_logs', function($join)
						{
							$join->on('quota_logs.voucher_id', '=', 'tmp_vouchers.id')
							->wherenull('quota_logs.deleted_at')
							;
						})
						->groupby('tmp_vouchers.id')
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
