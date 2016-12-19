<?php 

namespace App\Entities\Traits;

use App\Services\Entities\CustomerScopes\QuotaReferralScope;

/**
 * Apply scope to get total point of customer
 *
 * @author cmooy
 */
trait HasQuotaReferralTrait 
{
    /**
     * Boot the total point scope
     *
     * @return void
     */
    public static function bootHasQuotaReferralTrait()
    {
        static::addGlobalScope(new QuotaReferralScope);
    }
}