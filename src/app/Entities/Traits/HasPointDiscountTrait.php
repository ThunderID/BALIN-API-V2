<?php 

namespace App\Entities\Traits;

use App\Services\Entities\TransactionScopes\PointDiscountScope;

/**
 * Apply scope to get bill of sales transaction
 *
 * @author cmooy
 */
trait HasPointDiscountTrait 
{
    /**
     * Boot the Has Cost scope for a model to get Cost of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootHasPointDiscountTrait()
    {
        static::addGlobalScope(new PointDiscountScope);
    }
}