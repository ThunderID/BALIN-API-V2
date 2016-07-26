<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use App\Events\AuditStore;

/**
 * Used in Voucher model
 *
 * @author cmooy
 */
class VoucherObserver 
{
    /** 
     * observe voucher event created
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
        event(new AuditStore($model, 'voucher_added', 'Pembuatan voucher '.$model->code));

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    /** 
     * observe voucher event saving
     * 1. check is voucher used
     * 2. execute it there was no error
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
	{
		$errors 							= new MessageBag();

        //1. check is voucher used
        if($model->transactions->count())
        {
            $errors->add('Voucher', 'Tidak dapat mengubah voucher yang telah digunakan dalam transaksi.');
        }

		if($errors->count())
        {
            $model['errors']        		= $errors;

            return false;
        }

        return true;
	}

    /** 
     * observe voucher event saved
     * 1. execute it there was no error
     * 
     * @param $model
     * @return bool
     */
	public function saved($model)
	{
		$errors 							= new MessageBag();

		if($errors->count())
        {
            $model['errors']        		= $errors;

            return false;
        }

        return true;
	}

    /** 
     * observe voucher event deleting
     * 1. Check transaction used this voucher
     * 2. Delete quota logs
     * 3. execute it there was no error
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
    {
		$errors 							= new MessageBag();

    	//1. Check transaction used this voucher

        if($model->transactions()->count())
        {
            $errors->add('Voucher', 'Tidak dapat menghapus voucher yang telah digunakan dalam transaksi.');
        }

        //2. Delete quota logs
        foreach ($model->quotalogs as $key => $value) 
        {
            if(!$value->delete())
            {
                $errors->add('Voucher', $value->getError());
            }
        }

        if($errors->count())
        {
            $model['errors']        		= $errors;

            return false;
        }

        return true;
    }
}
