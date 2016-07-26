<?php 

namespace App\Models\Traits;

use App\Models\Scopes\CurrentPriceScope;

/**
 * Apply scope to get current price
 *
 * @author cmooy
 */
trait HasCurrentPriceTrait 
{
    /**
     * Boot the Has CurrentPrice scope for a model has price.
     *
     * @return void
     */
    public static function bootHasCurrentPriceTrait()
    {
        static::addGlobalScope(new CurrentPriceScope);
    }
}