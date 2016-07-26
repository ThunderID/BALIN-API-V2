<?php 

namespace App\Models\Traits;

use App\Models\Scopes\CurrentStatusScope;

/**
 * Apply scope to get current status
 *
 * @author cmooy
 */
trait HasCurrentStatusTrait 
{
    /**
     * Boot the Has CurrentStatus scope for a model has logs transaction.
     *
     * @return void
     */
    public static function bootHasCurrentStatusTrait()
    {
        static::addGlobalScope(new CurrentStatusScope);
    }
}