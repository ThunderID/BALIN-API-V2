<?php 

namespace App\Models\Traits;

use App\Models\Scopes\TotalPointScope;

/**
 * Apply scope to get total point of user
 *
 * @author cmooy
 */
trait HasTotalPointTrait 
{
    /**
     * Boot the Has TotalPoint trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasTotalPointTrait()
    {
        static::addGlobalScope(new TotalPointScope);
    }
}