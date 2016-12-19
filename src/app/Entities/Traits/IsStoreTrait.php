<?php 

namespace App\Entities\Traits;

use App\Services\Entities\OnlineStoreScopes\StoreScope;

/**
 * Apply scope to get bill of sales transaction
 *
 * @author cmooy
 */
trait IsStoreTrait 
{
    /**
     * Boot the Has Cost scope for a model to get Cost of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootIsStoreTrait()
    {
        static::addGlobalScope(new StoreScope);
    }
}