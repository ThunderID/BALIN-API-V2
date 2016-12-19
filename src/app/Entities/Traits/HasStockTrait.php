<?php 

namespace App\Entities\Traits;

use App\Services\Entities\StockScopes\StockScope;

/**
 * Apply scope to get current address
 *
 * @author cmooy
 */
trait HasStockTrait 
{
    /**
     * Boot the total point scope
     *
     * @return void
     */
    public static function bootHasStockTrait()
    {
        static::addGlobalScope(new StockScope);
    }
}