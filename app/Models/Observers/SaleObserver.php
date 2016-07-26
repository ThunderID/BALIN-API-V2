<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

/**
 * Used in Sale model
 *
 * @author cmooy
 */
class SaleObserver 
{
    /** 
     * observe sale event created
     * 1. change status to cart
     * 2. execute it there was no error
     * 
     * @param $model
     * @return bool
     */
	public function created($model)
	{
		$errors 							= new MessageBag();

		//1. change status to cart
        $result                             = $model->ChangeStatus($model, 'cart');
        if(!$result)
        {
            return false;
        }

		if($errors->count())
        {
            $model['errors']        		= $errors;

            return false;
        }

        return true;
	}

    /** 
     * observe sale event saving
     * 1. generate ref number
     * 2. generate unique number
     * 3. create transact_at
     * 4. check voucher
     * 5. execute it there was no error
     * 
     * @param $model
     * @return bool
     */
    public function saving($model)
    {
        $errors                             = new MessageBag();

        //1. Generate ref number
        $model->ref_number                  = $model->generateRefNumber($model);
        
        //2. Generate unique number
        if($model->status=='cart' || $model->status=='na')
        {
            $model->unique_number           = $model->generateUniqueNumber($model);
        }

        //3. create transact_at
        if(!in_array($model->status, ['canceled', 'abandoned', 'paid', 'shipping', 'delivered', 'na', 'wait', 'veritrans_processing_payment']))
        {
            $model->transact_at             = Carbon::now()->format('Y-m-d H:i:s');
        }

        //4. check voucher
        if($model->voucher_id != 0 && (!$model->voucher()->count() || $model->voucher->type=='promo_referral'))
        {
            $errors->add('Voucher', 'Voucher tidak valid.');
        }
        elseif($model->voucher()->count())
        {
            $result                         = $model->CountVoucherDiscount($model);

            if(!$result && count($model['errors']))
            {
                return false;
            }
            else
            {
                $model->voucher_discount    = $result;
            }
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    /** 
     * observe sale event saved
     * 1. credit voucher's quota
     * 2. execute it there was no error
     * 
     * @param $model
     * @return bool
     */
    public function saved($model)
    {
        $errors                             = new MessageBag();

        //1. credit voucher's quota
        if($model->voucher()->count())
        {
            $result                         = $model->CreditQuota($model->voucher, 'Penggunaan voucher untuk transaksi #'.$model->ref_number);
        
            if(!$result)
            {
                return false;
            }
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }
    
    /** 
     * observe sale event deleting
     * 1. disable delete function
     * 
     * @param $model
     * @return bool
     */
    public function deleting($model)
    {
        $model['errors']        		= 'Tidak dapat menghapus transaksi, silahkan batalkan.';

        return false;
    }
}
