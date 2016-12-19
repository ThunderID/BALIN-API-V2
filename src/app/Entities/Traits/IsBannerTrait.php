<?php 

namespace App\Entities\Traits;

use App\Services\Entities\OnlineStoreScopes\BannerScope;

/**
 * Apply scope to get bill of sales transaction
 *
 * @author cmooy
 */
trait IsBannerTrait 
{
    /**
     * Boot the Has Cost scope for a model to get Cost of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootIsBannerTrait()
    {
        static::addGlobalScope(new BannerScope);
    }
}