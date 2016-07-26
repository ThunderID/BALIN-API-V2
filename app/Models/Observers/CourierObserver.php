<?php namespace App\Models\Observers;

/**
 * Used in Courier model
 *
 * @author cmooy
 */
class CourierObserver 
{
	/** 
     * observe courier event deleting
     * 1. check courier relationship
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function deleting($model)
    {
        if($model->shipments()->count())
        {
            $model['errors']            = 'Tidak dapat menghapus Kurir yang pernah melakukan transaksi.';

            return false;
        }
        
        foreach ($model->shippingcosts as $key => $value) 
        {
            if(!$value->delete())
            {
                $model['errors']            = $value->getError();

                return false;
            }        
        }

        return true;
    }
}
