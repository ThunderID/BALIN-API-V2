<?php 

namespace App\Entities\Traits;

use App\Services\Entities\TransactionScopes\BuyScope;

/**
 * Apply scope to get bill of sales transaction
 *
 * @author cmooy
 */
trait IsBuyTrait 
{
    /**
     * Boot the Has Cost scope for a model to get Cost of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootIsBuyTrait()
    {
        static::addGlobalScope(new BuyScope);
    }
}