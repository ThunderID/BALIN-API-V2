<?php namespace App\Models\Observers;

/**
 * Used in Supplier model
 *
 * @author cmooy
 */
class SupplierObserver 
{
    /** 
     * observe supplier event deleting
     * 1. check supplier relationship
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function deleting($model)
    {
    	//1. check if supplier has transaction
        if($model->transactions()->count())
        {
            $model['errors']            = 'Tidak dapat menghapus supplier yang pernah melakukan transaksi.';

            return false;
        }

        return true;
    }
}
