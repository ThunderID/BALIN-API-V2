<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Models\Varian;

/**
 * Used in varian model
 *
 * @author cmooy
 */
class VarianObserver 
{
    /** 
     * observe product event saving
     * 1. check unique sku
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
    {
		$errors 						= new MessageBag();

		//1. Check unique SKU
        $sku                            = Varian::sku($model->sku)->notid($model->id)->first();

        if(!is_null($sku))
        {
			$errors->add('sku', 'SKU sudah terdaftar.');
        }
        
        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
    }

    /** 
     * observe product event deleting
     * 1. check varian relationship with transaction
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function deleting($model)
    {
		$errors 						= new MessageBag();

		/* --------------------------------DELETE VARIAN RELATIONSHIP--------------------------------------*/

        //1. Check varian relationship with transaction
        if($model->transactions()->count())
        {
            $errors->add('varian', 'Tidak dapat menghapus produk varian yang pernah di stok &/ order.');
        }

        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
    }
}
