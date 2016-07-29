<?php 

namespace App\Entities\Traits;

use App\Services\Entities\OnlineStoreScopes\StorePageScope;

/**
 * Apply scope to get bill of sales transaction
 *
 * @author cmooy
 */
trait IsStorePageTrait 
{
    /**
     * Boot the Has Cost scope for a model to get Cost of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootIsStorePageTrait()
    {
        static::addGlobalScope(new StorePageScope);
    }
}