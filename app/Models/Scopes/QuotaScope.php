<?php namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Scope to count total quota of voucher
 *
 * @return quota
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
		if($model->getTable()=='tmp_vouchers')
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
		else
		{
			$builder->selectraw('IFNULL(sum(quota_logs.amount),0) as quota_referral')
						->selectraw('IF(tmp_vouchers.type="referral", tmp_vouchers.code, "BALIN") as code_referral')
						->leftjoin('tmp_vouchers', function($join)
						{
							$join->on('tmp_vouchers.user_id', '=', 'users.id')
							->wherein('type', ['referral', 'promo_referral'])
							->wherenull('tmp_vouchers.deleted_at')
							;
						})
						->leftjoin('quota_logs', function($join)
						{
							$join->on('quota_logs.voucher_id', '=', 'tmp_vouchers.id')
							->wherenull('quota_logs.deleted_at')
							;
						})
						->groupby('users.id')
						;
		}
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
	    // unset($query->wheres['type']);
	}
}
