<?php 

namespace App\Entities\Traits;

use App\Services\Entities\ClusterScopes\CategoryScope;

/**
 * Apply scope to check category stuffs
 *
 * @author cmooy
 */
trait IsCategoryTrait 
{
    /**
     * Boot to check it is category or not
     *
     * @return void
     */
    public static function bootIsCategoryTrait()
    {
        static::addGlobalScope(new CategoryScope);
    }
}