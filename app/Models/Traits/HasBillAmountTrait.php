<?php 

namespace App\Models\Traits;

use App\Models\Scopes\BillAmountScope;

/**
 * Apply scope to get bill of sales transaction
 *
 * @author cmooy
 */
trait HasBillAmountTrait 
{
    /**
     * Boot the Has Amount scope for a model to get amount of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootHasBillAmountTrait()
    {
        static::addGlobalScope(new BillAmountScope);
    }
}