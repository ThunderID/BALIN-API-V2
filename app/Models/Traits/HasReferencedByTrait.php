<?php 

namespace App\Models\Traits;

use App\Models\Scopes\ReferencedByScope;

/**
 * Apply scope to get reference name of user
 *
 * @author cmooy
 */
trait HasReferencedByTrait 
{
    /**
     * Boot the Has ReferencedBy trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasReferencedByTrait()
    {
        static::addGlobalScope(new ReferencedByScope);
    }
}