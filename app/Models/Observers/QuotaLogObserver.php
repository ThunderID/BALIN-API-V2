<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Hash;

use App\Models\QuotaLog;
use App\Events\AuditStore;

/**
 * Used in QuotaLog
 *
 * @author cmooy
 */
class QuotaLogObserver 
{
    /** 
     * observe QuotaLog event created
     * 1. Audit
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function created($model)
    {
        $errors                             = new MessageBag();

        //1. audit
        if($model->voucher()->count())
        {
            event(new AuditStore($model, 'quota_added', 'Penambahan quota voucher '.$model->voucher->code));
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }
}
