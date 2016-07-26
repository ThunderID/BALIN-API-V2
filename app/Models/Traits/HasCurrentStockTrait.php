<?php 

namespace App\Models\Traits;

use App\Models\Scopes\CurrentStockScope;

/**
 * Apply scope to count current stock
 *
 * @author cmooy
 */
trait HasCurrentStockTrait 
{
	/**
     * Boot the Has CurrentPrice trait for a model has stock.
     *
     * @return void
     */
    public static function bootHasCurrentStockTrait()
    {
        static::addGlobalScope(new CurrentStockScope);
    }
}