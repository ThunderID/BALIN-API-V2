<?php 

namespace App\Models\Traits;

use App\Models\Scopes\ReferralOfScope;

/**
 * Apply scope to count total downline of users
 *
 * @author cmooy
 */
trait HasReferralOfTrait 
{
    /**
     * Boot the Has ReferralOf trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasReferralOfTrait()
    {
        static::addGlobalScope(new ReferralOfScope);
    }
}