<?php 

namespace App\Entities\Traits;

use App\Services\Entities\CustomerScopes\CodeReferralScope;

/**
 * Apply scope to get total point of customer
 *
 * @author cmooy
 */
trait HasCodeReferralTrait 
{
    /**
     * Boot the total point scope
     *
     * @return void
     */
    public static function bootHasCodeReferralTrait()
    {
        static::addGlobalScope(new CodeReferralScope);
    }
}