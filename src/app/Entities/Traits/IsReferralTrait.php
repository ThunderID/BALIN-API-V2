<?php 

namespace App\Entities\Traits;

use App\Services\Entities\VoucherScopes\ReferralScope;

/**
 * Apply scope to get total point of customer
 *
 * @author cmooy
 */
trait IsReferralTrait 
{
    /**
     * Boot the total point scope
     *
     * @return void
     */
    public static function bootIsReferralTrait()
    {
        static::addGlobalScope(new ReferralScope);
    }
}