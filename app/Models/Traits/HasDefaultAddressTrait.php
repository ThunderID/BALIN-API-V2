<?php 

namespace App\Models\Traits;

use App\Models\Scopes\DefaultAddressScope;

/**
 * Apply scope to get default Address
 *
 * @author cmooy
 */
trait HasDefaultAddressTrait 
{
    /**
     * Boot the Has DefaultAddress trait for a model has default Address.
     *
     * @return void
     */
    public static function bootHasDefaultAddressTrait()
    {
        static::addGlobalScope(new DefaultAddressScope);
    }
}