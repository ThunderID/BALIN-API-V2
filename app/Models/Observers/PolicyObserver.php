<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use App\Events\AuditStore;

/**
 * Used in Policy model
 *
 * @author cmooy
 */
class PolicyObserver 
{
    /** 
     * observe policy event created
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
        event(new AuditStore($model, 'policy_changed', 'Perubahan policy '.str_replace('_', ' ', $model['attributes']['type'])));

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    /** 
     * observe policy saving
     * 1. act if error or not
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
    {
        $errors                             = new MessageBag();

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    /** 
     * observe policy event deleting
     * 1. refuse delete
     * 
     * @param $model
     * @return bool
     */
    public function deleting($model)
    {
		$model['errors']            = 'Tidak dapat menghapus Pengaturan.';

		return false;

        return true;
    }
}
