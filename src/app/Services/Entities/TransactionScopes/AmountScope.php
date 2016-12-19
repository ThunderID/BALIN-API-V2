<?php 

namespace App\Services\Entities\TransactionScopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Scope to count total Amount of sales
 *
 * @return Amounts
 * @author cmooy
 */
class AmountScope implements ScopeInterface  
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
		if(isset($model->sort))
		{
			$builder->selectraw("
							sum(IFNULL((SELECT sum((price - discount) * quantity) FROM transaction_details WHERE transaction_details.transaction_id = transactions.id and transaction_details.deleted_at is null),0)
							+ IFNULL((SELECT sum(price) FROM transaction_extensions WHERE transaction_extensions.transaction_id = transactions.id and transaction_extensions.deleted_at is null),0)
							+ transactions.shipping_cost - transactions.voucher_discount - transactions.unique_number
							) as amount
						")
						->orderby($model->sort, $model->sort_param)
					;
		}
		else
		{
			$builder->selectraw("
						sum(IFNULL((SELECT sum((price - discount) * quantity) FROM transaction_details WHERE transaction_details.transaction_id = transactions.id and transaction_details.deleted_at is null),0)
						+ IFNULL((SELECT sum(price) FROM transaction_extensions WHERE transaction_extensions.transaction_id = transactions.id and transaction_extensions.deleted_at is null),0)
						+ transactions.shipping_cost - transactions.voucher_discount - transactions.unique_number
						) as amount
					")
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
	   	unset($query);
	}
}
