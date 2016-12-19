<?php 

namespace App\Entities\Traits;

use App\Services\Entities\AdminScopes\AdminScope;

/**
 * Apply scope to get bill of sales transaction
 *
 * @author cmooy
 */
trait IsAdminTrait 
{
    /**
     * Boot the Has Cost scope for a model to get Cost of transaction hasn't been paid.
     *
     * @return void
     */
    public static function bootIsAdminTrait()
    {
        static::addGlobalScope(new AdminScope);
    }
}