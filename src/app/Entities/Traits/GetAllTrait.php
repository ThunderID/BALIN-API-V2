<?php 

namespace App\Entities\Traits;

use App\Services\Entities\TransactionScopes\AllScope;

/**
 * Apply scope to get all of sales transaction attribute
 *
 * @author cmooy
 */
trait GetAllTrait 
{
    /**
     * Boot the Has Cost scope for a model to get Cost of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootGetAllTrait()
    {
        static::addGlobalScope(new AllScope);
    }
}