<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Carbon\Carbon;

/**
 * Used in TransactionLog model
 *
 * @author cmooy
 */
class TransactionLogObserver 
{
	/** 
	 * observe transaction log event creating
	 * 1. modify changed at
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function creating($model)
	{
		$errors                         = new MessageBag();

		//1. modify changed at
		if(!isset($model->changed_at))
		{
			$model->changed_at			= Carbon::now()->format('Y-m-d H:i:s');
		}

		return true;
	}
	/** 
	 * observe transaction log event saving
	 * 1. Check if transaction is sale
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function saving($model)
	{
		$errors 						= new MessageBag();

		//A. Check if transaction is sale
		if($model->sale()->count())
		{
			/** 
			* Switch scheme
			* 1. only allowed current status = abandoned if previous was cart
			* 2. only allowed current status = cart if previous was non or cart
			* 3. only allowed current status = wait if whole sale's items were sellable (available stock), and there were shipment address 
			* 4. only allowed current status = paid/packed if bills = 0 
			* 5. only allowed current status = shipping/delivery if receipt number not null 
			* 6. only allowed current status = canceled if bills != 0 
			*/
			switch($model->status)
			{
				case 'abandoned' :
					if($model->sale->status!='cart')
					{
						$errors->add('Log', 'Tidak dapat mengabaikan transaksi yang bukan di keranjang.');
					}
				break;
				case 'cart' :
					if($model->sale->status!='cart' && $model->sale->status!='na')
					{
						$errors->add('Log', 'Tidak dapat mengabaikan transaksi yang sudah checkout.');
					}
				break;
				case 'wait' :
					$details                = $model->sale->transactiondetails;

					foreach ($details as $key => $value) 
					{
						if($value['varian']['current_stock'] < $value['quantity'])
						{
							$errors->add('Log', 'Stok '.$value['varian']['product']['name'].' ukuran '.$value['varian']['size']. ' tidak mencukupi');
						}
					}
					if(!$details->count())
					{
						$errors->add('Detail', 'Tidak dapat checkout tanpa produk.');
					}
					if(!$errors->count() && (!$model->sale->shipment()->count() || !$model->sale->shipment->address()->count()))
					{
						$errors->add('Log', 'Tidak dapat checkout tanpa alamat pengiriman.');
					}
				break;
				case 'paid' : case 'packed' :
					if(in_array($model->sale->status, ['cart']))
					{
						$errors->add('Log', 'Tidak dapat memvalidasi/packing transaksi yang bukan belum di checkout.');
					}

					if($model->sale->bills!=0)
					{
						$errors->add('Log', 'Pembayaran masih kurang sebesar '.$model->sale->bills.'.');
					}
				break;
				case 'shipping': case 'delivered' :
					if($model->sale->bills!=0)
					{
						$errors->add('Log', 'Pembayaran masih kurang sebesar '.$model->sale->bills.'.');
					}

					if($model->type=='sell' && (!$model->sale->shipment()->count() || is_null($model->sale->shipment->receipt_number)))
					{
						$errors->add('Log', 'Tidak dapat checkout tanpa resi pengiriman.');
					}
				break;
				case 'canceled' :
					if($model->sale->bills==0)
					{
						$errors->add('Log', 'Tidak dapat membatalkan transaksi yang sudah dibayar.');
					}
					elseif($model->sale->status!='wait' && $model->sale->status!='veritrans_processing_payment')
					{
						$errors->add('Log', 'Tidak dapat mengabaikan transaksi yang belum di checkout.');
					}
				break;
			}
		}
		
		if($errors->count())
		{
			$model['errors'] 		= $errors;

			return false;
		}

		return true;
	}

	/** 
	 * observe transaction log event saved
	 * 1. Check if transaction is sale
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function saved($model)
	{
		$errors                                 = new MessageBag();
		
		//A. Check if transaction is sale
		if($model->sale()->count())
		{
		   /**
			* Switch scheme
			* 1. current status = cart, save audit abandoned
			* 2. current status = wait, credit point, if full-paid-points,  change status to paid
			* 3. current status = paid, add quota and point for upline, then save audit payment
			* 4. current status = shipping, save audit shipping
			* 5. current status = delivered, save audit delivered
			* 6. current status = canceled, revert point paid, save audit canceled
			*/
			switch($model->status)
			{
				case 'cart' :
					$prev_sale 					= \App\Models\Sale::notid($model->sale_id)->userid($model->sale->user_id)->status('cart')->first();
					if($prev_sale)
					{
						$result                 = $model->ChangeStatus($prev_sale, 'abandoned');
					}
				break;
				case 'wait' :
					$result                     = $model->CreditPoint($model->sale);

					if(!$result && count($model['errors']))
					{
						return false;
					}

					if($result==0)
					{
						$result                 = $model->ChangeStatus($model->sale, 'paid', 'Full Point', ' + 1 second ');
					}
				break;
				case 'paid' :
					$result                     = $model->AddQuotaForUpline($model->sale);

					if(!$result)
					{
						return false;
					}

					$result                 	= $model->AddPointForUpline($model->sale);
				break;
				case 'shipping' :
				break;
				case 'delivered' :
				break;
				case 'canceled' :
					$result                     = $model->RevertPoint($model->sale);
				break;
			}

			if(isset($result) && !$result)
			{
				return false;
			}
		}

		if($errors->count())
		{
			$model['errors']        = $errors;

			return false;
		}

		return true;
	}
	
	/** 
	 * observe transaction log event deleting
	 * 1. temporary refuse delete log
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function deleting($model)
	{
		$errors 						= new MessageBag();

		$errors->add('log', 'Tidak dapat menghapus log transaksi.');

		if($errors->count())
		{
			$model['errors'] 		= $errors;

			return false;
		}

		return true;
	}
}
