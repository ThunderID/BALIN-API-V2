<?php 

namespace App\Entities\Traits;

use App\Services\Entities\CustomerScopes\TotalReferenceScope;

/**
 * Apply scope to get total point of customer
 *
 * @author cmooy
 */
trait HasTotalReferenceTrait 
{
    /**
     * Boot the total point scope
     *
     * @return void
     */
    public static function bootHasTotalReferenceTrait()
    {
        static::addGlobalScope(new TotalReferenceScope);
    }
}