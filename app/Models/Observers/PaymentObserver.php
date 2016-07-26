<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Log;
/**
 * Used in Payment model
 *
 * @author cmooy
 */
class PaymentObserver 
{
	/** 
	 * observe payment event saving
	 * 1. check payment
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function saving($model)
	{
		$errors                             = new MessageBag();

		//1. check payment
		if($model->sale()->count() && (is_null($model->id) || isset($model->getDirty()['amount'])))
		{
			$result                         = $model->CheckPaid($model->sale, $model['amount']);

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
	 * observe payment event saved
	 * 1. change status
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function saved($model)
	{
		$errors                             = new MessageBag();

		//1. change status
		if($model->sale()->count() && $model->sale->status != 'paid')
		{
			$result							= $model->ChangeStatus($model->sale, 'paid');
			
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
	 * observe payment event deleting
	 * 1. check relationship with transaction
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function deleting($model)
	{
		$errors                             = new MessageBag();

		//1. check relationship with transaction
		if($model->sale()->count())
		{
			$errors->add('Payment', 'Tidak bisa menghapus data payment yang sudah divalidasi.');
		}

		if($errors->count())
		{
			$model['errors']                = $errors;

			return false;
		}

		return true;
	}
}
