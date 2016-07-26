<?php 

namespace App\Entities\Traits;

use App\Services\Entities\TransactionScopes\AmountScope;

/**
 * Apply scope to get Amount of sales transaction
 *
 * @author cmooy
 */
trait HasAmountTrait 
{
    /**
     * Boot the Has Cost scope for a model to get Cost of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootHasAmountTrait()
    {
        static::addGlobalScope(new AmountScope);
    }
}