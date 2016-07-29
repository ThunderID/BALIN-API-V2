<?php 

namespace App\Entities\Traits;

use App\Services\Entities\PriceScopes\PriceScope;

/**
 * Apply scope to get current address
 *
 * @author cmooy
 */
trait HasPriceTrait 
{
    /**
     * Boot the total price scope
     *
     * @return void
     */
    public static function bootHasPriceTrait()
    {
        static::addGlobalScope(new PriceScope);
    }
}