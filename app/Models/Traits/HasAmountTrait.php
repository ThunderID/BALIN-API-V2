<?php 

namespace App\Models\Traits;

use App\Models\Scopes\AmountScope;

/**
 * Apply scope to get amount of transaction
 *
 * @author cmooy
 */
trait HasAmountTrait 
{
    /**
     * Boot the Has Amount scope for a model to get amount of shop.
     *
     * @return void
     */
    public static function bootHasAmountTrait()
    {
        static::addGlobalScope(new AmountScope);
    }

    /**
     * scope to get precise amount.
     *
     * @return void
     */
    public function scopeAmount($query, $variable)
    {
        return $query->havingraw("
						sum(IFNULL((SELECT sum((price - discount) * quantity) FROM transaction_details WHERE transaction_details.transaction_id = transactions.id and transaction_details.deleted_at is null),0)
						+ IFNULL((SELECT sum(amount) FROM point_logs WHERE point_logs.reference_id = transactions.id and point_logs.deleted_at is null and point_logs.reference_type like '%Transaction%' and point_logs.amount < 0),0)
						- IFNULL((SELECT sum(amount) FROM payments WHERE payments.transaction_id = transactions.id and payments.deleted_at is null),0)
						+ transactions.shipping_cost - transactions.voucher_discount - transactions.unique_number
						) = ".$variable
					);
    }
}