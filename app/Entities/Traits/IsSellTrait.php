<?php 

namespace App\Entities\Traits;

use App\Services\Entities\TransactionScopes\SellScope;

/**
 * Apply scope to get bill of sales transaction
 *
 * @author cmooy
 */
trait IsSellTrait 
{
    /**
     * Boot the Has Cost scope for a model to get Cost of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootIsSellTrait()
    {
        static::addGlobalScope(new SellScope);
    }
}