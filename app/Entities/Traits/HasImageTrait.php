<?php 

namespace App\Entities\Traits;

use App\Services\Entities\ImageScopes\ImageScope;

/**
 * Apply scope to get default Image
 *
 * @author cmooy
 */
trait HasImageTrait 
{
    /**
     * Boot the total point scope
     *
     * @return void
     */
    public static function bootHasImageTrait()
    {
        static::addGlobalScope(new ImageScope);
    }
}