<?php 

namespace App\Entities\Traits;

use App\Services\Entities\OnlineStoreScopes\PolicyScope;

/**
 * Apply scope to get bill of sales transaction
 *
 * @author cmooy
 */
trait IsPolicyTrait 
{
    /**
     * Boot the Has Cost scope for a model to get Cost of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootIsPolicyTrait()
    {
        static::addGlobalScope(new PolicyScope);
    }
}