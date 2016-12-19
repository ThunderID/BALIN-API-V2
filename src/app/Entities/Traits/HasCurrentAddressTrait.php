<?php 

namespace App\Entities\Traits;

use App\Services\Entities\AddressScopes\CurrentAddressScope;

/**
 * Apply scope to get current address
 *
 * @author cmooy
 */
trait HasCurrentAddressTrait 
{
    /**
     * Boot the total point scope
     *
     * @return void
     */
    public static function bootHasCurrentAddressTrait()
    {
        static::addGlobalScope(new CurrentAddressScope);
    }
}