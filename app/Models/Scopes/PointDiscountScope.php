<?php namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Scope to count total amount of transaction
 *
 * @return amount
 * @author cmooy
 */
class PointDiscountScope implements ScopeInterface  
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
		$builder->selectraw("
					sum(IFNULL((SELECT abs(sum(amount)) FROM point_logs WHERE point_logs.reference_id = transactions.id and point_logs.deleted_at is null and point_logs.reference_type like '%Sale%' and point_logs.amount < 0),0)
					) as point_discount
				")
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
	    // unset($query->wheres['type']);
	}
}
