<?php 

namespace App\Entities\Traits;

use App\Services\Entities\CustomerScopes\CustomerScope;

/**
 * Apply scope to get bill of sales transaction
 *
 * @author cmooy
 */
trait IsCustomerTrait 
{
    /**
     * Boot the Has Cost scope for a model to get Cost of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootIsCustomerTrait()
    {
        static::addGlobalScope(new CustomerScope);
    }
}