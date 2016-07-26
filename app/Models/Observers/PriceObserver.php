<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Hash;

use App\Models\Price;
use App\Events\AuditStore;

/**
 * Used in Price
 *
 * @author cmooy
 */
class PriceObserver 
{
    /** 
     * observe Price event saved
     * 1. Audit
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function saved($model)
    {
        $errors                             = new MessageBag();

        //1. audit
        if($model->product()->count())
        {
            event(new AuditStore($model, 'price_changed', 'Perubahan harga '.$model->product->name));
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }
}
