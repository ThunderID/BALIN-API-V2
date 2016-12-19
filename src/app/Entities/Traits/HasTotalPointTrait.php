<?php 

namespace App\Entities\Traits;

use App\Services\Entities\CustomerScopes\TotalPointScope;

/**
 * Apply scope to get total point of customer
 *
 * @author cmooy
 */
trait HasTotalPointTrait 
{
    /**
     * Boot the total point scope
     *
     * @return void
     */
    public static function bootHasTotalPointTrait()
    {
        static::addGlobalScope(new TotalPointScope);
    }
}