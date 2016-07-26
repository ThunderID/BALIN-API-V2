<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

/**
 * Used in Transaction Extension model
 *
 * @author cmooy
 */
class TransactionExtensionObserver 
{
	/** 
	 * observe transaction detail event saving
	 * 1. Check transaction status
	 * 2. Check duplicate product extension
	 * 3. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function saving($model)
	{
		$errors                             = new MessageBag();

		//1. check transactions tatus
		if($model->sale()->count() && $model->sale->status!='cart' && count($model->getDirty()))
		{
			$errors->add('Detail', 'Tidak dapat menambahkan item baru. Silahkan membuat nota baru.');
		}

		//2. Check duplicate product extension
		if(is_null($model->id))
		{
			$id 							= 0;
		}
		else
		{
			$id 							= $model->id;
		}

		$check_prev_trans					= \App\Models\TransactionExtension::transactionid($model->transaction_id)->productextensionid($model->product_extension_id)->notid($id)->first();

		if($check_prev_trans)
		{
			$errors->add('Detail', 'Tidak dapat menyimpan 2 record untuk product extension id yang sama.');
		}

		if($errors->count())
		{
			$model['errors']                = $errors;

			return false;
		}

		return true;
	}
}
