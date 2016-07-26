<?php 

namespace App\Models\Traits;

use App\Models\Scopes\QuotaScope;

/**
 * Apply scope to get quota of voucher
 *
 * @author cmooy
 */
trait HasQuotaTrait 
{
    /**
     * Boot the Has Quota scope for a model to get quota of voucher.
     *
     * @return void
     */
    public static function bootHasQuotaTrait()
    {
        static::addGlobalScope(new QuotaScope);
    }
}