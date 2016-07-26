<?php 

namespace App\Entities\Traits;

use App\Services\Entities\TransactionScopes\ExtendCostScope;

/**
 * Apply scope to get bill of sales transaction
 *
 * @author cmooy
 */
trait HasExtendCostTrait 
{
    /**
     * Boot the Has Cost scope for a model to get Cost of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootHasExtendCostTrait()
    {
        static::addGlobalScope(new ExtendCostScope);
    }
}