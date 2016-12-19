<?php 

namespace App\Entities\Traits;

use App\Services\Entities\VoucherScopes\QuotaScope;

/**
 * Apply scope to get total point of customer
 *
 * @author cmooy
 */
trait HasQuotaTrait 
{
    /**
     * Boot the total point scope
     *
     * @return void
     */
    public static function bootHasQuotaTrait()
    {
        static::addGlobalScope(new QuotaScope);
    }
}