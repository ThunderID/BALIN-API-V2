<?php 

namespace App\Entities\Traits;

use App\Services\Entities\VoucherScopes\VoucherScope;

/**
 * Apply scope to get total point of customer
 *
 * @author cmooy
 */
trait IsVoucherTrait 
{
    /**
     * Boot the total point scope
     *
     * @return void
     */
    public static function bootIsVoucherTrait()
    {
        static::addGlobalScope(new VoucherScope);
    }
}