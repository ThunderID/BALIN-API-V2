<?php 

namespace App\Entities\Traits;

use App\Services\Entities\CustomerScopes\ReferenceNameScope;

/**
 * Apply scope to get total point of customer
 *
 * @author cmooy
 */
trait HasReferenceNameTrait 
{
    /**
     * Boot the total point scope
     *
     * @return void
     */
    public static function bootHasReferenceNameTrait()
    {
        static::addGlobalScope(new ReferenceNameScope);
    }
}