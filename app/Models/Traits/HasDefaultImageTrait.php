<?php 

namespace App\Models\Traits;

use App\Models\Scopes\DefaultImageScope;

/**
 * Apply scope to get default image
 *
 * @author cmooy
 */
trait HasDefaultImageTrait 
{
    /**
     * Boot the Has DefaultImage trait for a model has default image.
     *
     * @return void
     */
    public static function bootHasDefaultImageTrait()
    {
        static::addGlobalScope(new DefaultImageScope);
    }
}