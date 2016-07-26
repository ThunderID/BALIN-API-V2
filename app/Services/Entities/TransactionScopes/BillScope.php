<?php 

namespace App\Services\Entities\TransactionScopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Scope to count total bill of sales
 *
 * @return bills
 * @author cmooy
 */
class BillScope implements ScopeInterface  
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
		$builder
				->selectraw("
						sum(IFNULL((SELECT sum((price - discount) * quantity) FROM transaction_details WHERE transaction_details.transaction_id = transactions.id and transaction_details.deleted_at is null),0)
						+ IFNULL((SELECT sum(price) FROM transaction_extensions WHERE transaction_extensions.transaction_id = transactions.id and transaction_extensions.deleted_at is null),0)
						+ IFNULL((SELECT sum(amount) FROM point_logs WHERE point_logs.reference_id = transactions.id and point_logs.deleted_at is null and point_logs.reference_type like '%Sale%' and point_logs.amount < 0),0)
						- IFNULL((SELECT sum(amount) FROM payments WHERE payments.transaction_id = transactions.id and payments.deleted_at is null),0)
						+ transactions.shipping_cost - transactions.voucher_discount - transactions.unique_number
						) as bills
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
	    unset($query);
	}
}
