<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

/**
 * Used in Address model
 *
 * @author cmooy
 */
class AddressObserver 
{
    /** 
     * observe address event updating
     * 1. check if zipcode updated
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function updating($model)
    {
        $errors                             = new MessageBag();

        //1. check if zipcode updating
        if(isset($model->getDirty()['zipcode']))
        {
            foreach ($model->shipments as $key => $value) 
            {
                if($value->transaction()->count() && $value->transaction->status!='wait')
                {
                    $errors->add('Address', 'Tidak dapat mengubah kode pos pesanan yang sudah checkout.');
                }
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
     * observe address event deleting
     * 1. check if zipcode updated
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function deleting($model)
    {
        $errors                             = new MessageBag();

        //1. check if address was destination
        if($model->shipments()->count())
        {
            $errors->add('Address', 'Tidak dapat menghapus alamat yang pernah digunakan dalam pengiriman.');
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }
}
