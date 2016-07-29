<?php 

namespace App\Entities\Traits;

use App\Services\Entities\ClusterScopes\TagScope;

/**
 * Apply scope to check Tag stuffs
 *
 * @author cmooy
 */
trait IsTagTrait 
{
    /**
     * Boot to check it is Tag or not
     *
     * @return void
     */
    public static function bootIsTagTrait()
    {
        static::addGlobalScope(new TagScope);
    }
}