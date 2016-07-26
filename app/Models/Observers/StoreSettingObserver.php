<?php namespace App\Models\Observers;

/**
 * Used in StoreSetting, Slider, StorePage, Store Model
 *
 * @author cmooy
 */
class StoreSettingObserver 
{
	/** 
     * observe store setting event deleting
     * 1. refuse delete
     * 
     * @param $model
     * @return bool
     */
    public function deleting($model)
    {
		$model['errors']            = 'Tidak dapat menghapus Pengaturan.';

		return false;
    }
}
