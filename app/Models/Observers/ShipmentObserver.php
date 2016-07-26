<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Models\ShippingCost;
use App\Models\Sale;

/**
 * Used in Shipment model
 *
 * @author cmooy
 */
class ShipmentObserver 
{
	/** 
	 * observe payment event saving
	 * 1. check haven't been paid
	 * 2. recalculate shipping_cost
	 * 3. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function saving($model)
	{
		$errors                             = new MessageBag();

		if($model->package=='')
		{
			$model->package 				= 'regular';
		}
		
		//1. check haven't been paid
		if($model->sale()->count() && !in_array($model->sale->status, ['na', 'cart', 'wait']) && isset($model->getDirty()['address_id']))
		{
			$errors->add('Shipment', 'Tidak dapat mengubah destinasi pengiriman.');
		}

		//2. recalculate shipping_cost
		if($model->address()->count())
		{
			$shippingcost                       = ShippingCost::courierid($model->courier_id)->postalcode($model->address->zipcode)->first();

			if($shippingcost && $model->sale()->count() && $model->sale->transactiondetails()->count())
			{
				$shipping_cost                  = $model->CountShippingCost($model->sale->transactiondetails, $shippingcost['cost']);
			   
				$sale							= Sale::findorfail($model->transaction_id);

				$sale->fill(['shipping_cost' => $shipping_cost]);
				
				if(!$sale->save())
				{
					$errors->add('Shipment', $sale->getError());
				}
			}
			else
			{
				$errors->add('Shipment', 'Tidak ada kurir ke tempat anda (Silahkan periksa kembali kode pos anda).');
			}
		}
		elseif($model->sale()->count())
		{
			$sale							= Sale::findorfail($model->transaction_id);

			$sale->fill(['shipping_cost' => 0]);
			
			if(!$sale->save())
			{
				$errors->add('Shipment', $sale->getError());
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
	 * observe payment event updated
	 * 1. recalculate shipping_cost
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function updated($model)
	{
		$errors                             = new MessageBag();

		//1. check receipt_number
		if(!is_null($model->receipt_number) && $model->sale()->count())
		{
			$result                             = $model->ChangeStatus($model->sale, 'shipping');

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
	 * 1. recalculate shipping_cost
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function deleting($model)
	{
		$errors                             = new MessageBag();

		//1. check receipt_number
		if(!is_null($model->receipt_number))
		{
			$errors->add('Shipment', 'Tidak dapat menghapus data barang yang telah dikirim.');
		}

		if($errors->count())
		{
			$model['errors']                = $errors;

			return false;
		}

		return true;
	}
}
