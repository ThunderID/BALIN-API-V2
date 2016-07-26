<?php 

namespace App\Models\Traits;

use App\Models\Scopes\PointDiscountScope;

/**
 * Apply scope to get amount of transaction
 *
 * @author cmooy
 */
trait HasPointDiscountTrait 
{
    /**
     * Boot the Has Amount scope for a model to get amount of shop.
     *
     * @return void
     */
    public static function bootHasPointDiscountTrait()
    {
        static::addGlobalScope(new PointDiscountScope);
    }
}