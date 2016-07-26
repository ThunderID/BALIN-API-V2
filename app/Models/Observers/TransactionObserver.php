<?php namespace App\Models\Observers;

/**
 * Used in Transaction model
 *
 * @author cmooy
 */
class TransactionObserver 
{
    /** 
     * observe transaction event saving
     * 1. refuse since modify must goes thru successors' model
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
	{
		$model['errors']			= 'Tidak dapat menyimpan transaksi tanpa melalui sale atau purchase';

		return false;
	}

	/** 
     * observe transaction event deleting
     * 1. refuse since modify must goes thru successors' model
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$model['errors']			= 'Tidak dapat menghapus transaksi tanpa melalui sale atau purchase';
		
		return false;
	}
}
