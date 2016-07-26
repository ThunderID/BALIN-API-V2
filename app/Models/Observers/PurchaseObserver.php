<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Carbon\Carbon;

/**
 * Used in Purchase model
 *
 * @author cmooy
 */
class PurchaseObserver 
{
    /** 
     * observe purchase event creating
     * 1. generate transaction date
     * 2. generate transaction ref_number
     * 3. execute it there was no error
     * 
     * @param $model
     * @return bool
     */
	public function creating($model)
	{
		$errors 							= new MessageBag();

        //1. generate transaction date
        if(!isset($model->transact_at))
        {
            $model->transact_at             = Carbon::now()->format('Y-m-d H:i:s');
        }

        //2. generate transaction ref_number
        $model->ref_number                  = $model->generateRefNumber($model);

		if($errors->count())
        {
            $model['errors']        		= $errors;

            return false;
        }

        return true;
	}

    /** 
     * observe purchase event created
     * 1. change status to delivered
     * 2. execute it there was no error
     * 
     * @param $model
     * @return bool
     */
	public function created($model)
	{
		$errors 							= new MessageBag();

		//1. change status to delivered
        $result                             = $model->ChangeStatus($model, 'delivered', 'stock');

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
     * observe purchase event deleting
     * 1. remove transaction details
     * 2. remove transaction logs
     * 3. execute it there was no error
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
    {
		$errors 							= new MessageBag();

    	//1. Remove all details

        foreach ($model->transactiondetails as $key => $value) 
        {
            if(!$value->delete())
            {
            	$errors->add('Log', $value->getError());
            }
        }

    	//2. Remove all logs
        foreach ($model->transactionlogs as $key => $value) 
        {
            if(!$value->delete())
            {
            	$errors->add('Log', $value->getError());
            }
        }

        if($errors->count())
        {
            $model['errors']        		= $errors;

            return false;
        }

        return true;
    }
}
