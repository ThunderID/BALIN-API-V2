<?php 

namespace App\Models\Traits;

use App\Models\Scopes\ExtendAmountScope;

/**
 * Apply scope to get bill of sales transaction
 *
 * @author cmooy
 */
trait HasExtendAmountTrait 
{
    /**
     * Boot the Has Amount scope for a model to get amount of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootHasExtendAmountTrait()
    {
        static::addGlobalScope(new ExtendAmountScope);
    }
}