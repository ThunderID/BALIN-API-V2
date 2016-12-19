<?php 

namespace App\Entities\Traits;

use App\Services\Entities\OnlineStoreScopes\SliderScope;

/**
 * Apply scope to get bill of sales transaction
 *
 * @author cmooy
 */
trait IsSliderTrait 
{
    /**
     * Boot the Has Cost scope for a model to get Cost of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootIsSliderTrait()
    {
        static::addGlobalScope(new SliderScope);
    }
}