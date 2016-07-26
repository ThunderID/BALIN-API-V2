<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

/**
 * Used in TransactionDetail model
 *
 * @author cmooy
 */
class TransactionDetailObserver 
{
	/** 
	 * observe transaction detail event saving
	 * 1. Check transactions tatus
	 * 2. Check duplicate varian
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

		//2. Check duplicate varian
		if(is_null($model->id))
		{
			$id 							= 0;
		}
		else
		{
			$id 							= $model->id;
		}

		$check_prev_trans					= \App\Models\TransactionDetail::transactionid($model->transaction_id)->varianid($model->varian_id)->notid($id)->first();

		if($check_prev_trans)
		{
			$errors->add('Detail', 'Tidak dapat menyimpan 2 record untuk varian id yang sama.');
		}

		if($errors->count())
		{
			$model['errors']                = $errors;

			return false;
		}

		return true;
	}
}
