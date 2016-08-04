<?php

namespace App\Services\Policies;

use App\Entities\Sale;
use App\Entities\Purchase;
use App\Entities\TransactionLog;
use App\Entities\TransactionExtension;
use App\Entities\TransactionDetail;
use App\Entities\Address;
use App\Entities\Shipment;
use App\Entities\Voucher;
use App\Entities\QuotaLog;
use App\Entities\PointLog;
use App\Entities\User;
use App\Entities\Transaction;
use App\Entities\StoreSetting;

use Illuminate\Support\MessageBag;

use App\Contracts\Policies\ProceedTransactionInterface;

class ProceedTransaction implements ProceedTransactionInterface
{
	public $errors;
	public $sale;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 	= new MessageBag;
	}

	public function store(array $sale)
	{
		$stored_sale	= Sale::findornew($sale['id']);
		$stored_sale->fill($sale);

		if(!$stored_sale->save())
		{
			$this->errors->add('Sale', $stored_sale->getError());
		}

		$this->sale		= $stored_sale;
	}
	
	public function storepurchase(array $purchase)
	{
		$stored_purchase		= Purchase::findornew($purchase['id']);
		$stored_purchase->fill($purchase);

		if(!$stored_purchase->save())
		{
			$this->errors->add('Purchase', $stored_purchase->getError());
		}

		$this->sale			= $stored_purchase;
	}
	

	public function storesaleitem(Sale $sale, array $transaction_details)
	{
		foreach ($transaction_details as $key => $value) 
		{
			$tdetail 	= TransactionDetail::transactionid($sale->id)->varianid($value['varian_id'])->first();

			if($tdetail)
			{
				$tdetail->fill($value);
			}
			else
			{
				$tdetail = new TransactionDetail;
				$tdetail->fill($value);
			}

			$tdetail->transaction_id = $sale->id;

			if(!$tdetail->save())
			{
				$this->errors->add('Sale', $tdetail->getError());
			}
		}
	}

	public function storepurchaseitem(Purchase $purchase, array $transaction_details)
	{
		foreach ($transaction_details as $key => $value) 
		{
			$tdetail 	= TransactionDetail::transactionid($purchase->id)->varianid($value['varian_id'])->first();

			if($tdetail)
			{
				$tdetail->fill($value);
			}
			else
			{
				$tdetail = new TransactionDetail;
				$tdetail->fill($value);
			}

			$tdetail->transaction_id = $purchase->id;

			if(!$tdetail->save())
			{
				$this->errors->add('Purchase', $tdetail->getError());
			}
		}
	}

	public function storepackingornament(Sale $sale, array $transaction_extensions)
	{
		foreach ($transaction_extensions as $key => $value) 
		{
			$textension 	= TransactionExtension::transactionid($sale->id)->productextensionid($value['product_extension_id'])->first();

			if($textension)
			{
				$textension->fill($value);
			}
			else
			{
				$textension = new TransactionExtension;
				$textension->fill($value);
			}

			$textension->transaction_id = $sale->id;

			if(!$textension->save())
			{
				$this->errors->add('Sale', $textension->getError());
			}
		}
	}

	public function shippingaddress(Sale $sale, array $shipment)
	{
		$address 						= Address::phone($shipment['address']['phone'])->address($shipment['address']['address'])->zipcode($shipment['address']['zipcode'])->first();

		if(!$address)
		{
			$address 					= new Address;
			
			$address->fill($shipment['address']);

			if(!$address->save())
			{
				$this->errors->add('Sale', $address->getError());
			}	
		}

		$shipment_new					= Shipment::transactionid($sale->id)->courierid($shipment['courier_id'])->addressid($address->id)->first();
		
		if($shipment_new)
		{
			$shipment_new->fill($shipment);
		}
		else
		{
			$shipment_new 				= new Shipment;
			$shipment_new->fill($shipment);
		}

		$shipment_new->transaction_id 	= $sale->id;
		$shipment_new->address_id 		= $address->id;

		if(!$shipment_new->save())
		{
			$this->errors->add('Sale', $shipment_new->getError());
		}
	}

	public function creditquotavoucher(Voucher $voucher, $quota, $notes = '')
	{
		if($voucher)
		{
			$quota_log 					= new QuotaLog;

			$quota_log->fill(['voucher_id' => $voucher->id, 'amount' => $quota, 'notes' => $notes]);

			if(!$quota_log->save())
			{
				$this->errors->add('Sale', $quota_log->getError());
			}	
		}
	}

	public function creditbalinpoint(User $user, Sale $sale, $pointdiscount)
	{
		//cek all  in debit active point
		$points								= PointLog::userid($user->id)->onactive('now')->debit(true)->get();

		//count leftover active point
		$sumpoints							= PointLog::userid($user->id)->onactive('now')->sum('amount');

		$idx								= 0;
		$currentamount						= 0;
		$paidamount							= $pointdiscount;

		while($paidamount <= $pointdiscount && $points && isset($points[$idx]) && $sumpoints > 0)
		{
			$sumpoints						= PointLog::userid($user->id)->onactive('now')->sum('amount');
	
			//count left over point per debit to credit
			$currentamount					= $points[$idx]['amount'];

			foreach($points[$idx]->pointlogs as $key => $value)
			{
				$currentamount				= $currentamount + $value['amount'];
			}

			//if leftover more than 0
			if($currentamount > 0 && $currentamount >= $paidamount)
			{
				$camount					= 0 - $paidamount;
			}
			else
			{
				$camount					= 0 - $currentamount;
			}

			if($currentamount > 0)
			{
				$point						= new PointLog;
				$point->fill([
						'user_id'			=> $points[$idx]->user_id,
						'reference_id'		=> $sale->id,
						'reference_type'	=> get_class($sale),
						'point_log_id'		=> $points[$idx]->id,
						'amount'			=> $camount,
						'expired_at'		=> $points[$idx]->expired_at,
						'notes'				=> 'Pembayaran Belanja #'.$sale->ref_number,
					]);

				if(!$point->save())
				{
					$this->errors->add('Sale', $point->getError());
				}

				$paidamount					= $paidamount - $camount;
			}

			$idx++;
		}
	}

	public function revertbalinpoint(Sale $sale)
	{
		foreach ($sale->paidpointlogs as $key => $value) 
		{
			if($value->amount < 0)
			{
				$point                      = new PointLog;
				$point->fill([
						'user_id'           => $value->user_id,
						'point_log_id'      => $value->id,
						'reference_id'     	=> $sale->id,
						'reference_type'    => get_class($sale),
						'amount'            => 0 - $value->amount,
						'expired_at'        => $value->expired_at,
						'notes'             => 'Revert Belanja #'.$sale->ref_number,
					]);
		
				if(!$point->save())
				{
					$this->errors->add('Canceled', $point->getError());
				}
			}
		}
	}

	public function updatestatus(Transaction $sale, string $status)
	{
		if(strtolower($sale->status) != strtolower($status))
		{
			$new_status				= new TransactionLog;
			$new_status				= $new_status->fill(['status' => $status, 'transaction_id' => $sale['id'], 'changed_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')]);

			if(!$new_status->save())
			{
				$this->errors->add('Status', $new_status->getError());
			}

		}
		
		if(str_is('*Purchase', get_class($sale)))
		{
			$this->sale			= get_class($sale)::id($sale['id'])->with(['transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'supplier'])->first();
		}
		else
		{
			$this->sale			= get_class($sale)::id($sale['id'])->with(['voucher', 'transactionlogs', 'customer', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'paidpointlogs', 'payment', 'shipment', 'shipment.address', 'shipment.courier', 'transactionextensions', 'transactionextensions.productextension'])->first();
		}
	}

	public function grantupline(Sale $sale)
	{
		if($sale->customer()->count())
		{
			$customer 				= $sale->customer;

			$upline					= PointLog::userid($customer->user_id)->referencetype(['App\Models\User', 'App\Entities\User'])->first();

			$point					= StoreSetting::type('downline_purchase_bonus')->Ondate('now')->first();

			$expired				= StoreSetting::type('downline_purchase_bonus_expired')->Ondate('now')->first();

			$whoisupline                        = 0;

			if($upline  && $upline->reference()->count() && $upline->reference->referral()->count())
			{
				$whoisupline                    = $upline->reference->referral->value;
			}
			

			if($upline && $point && $expired  && $whoisupline == 0 && $upline->reference()->count() && $upline->reference->referral()->count())
			{
				$pointlog                       = new PointLog;

				$pointlog->fill([
						'user_id'				=> $upline->reference_id,
						'reference_id'			=> $sale->id,
						'reference_type'		=> get_class($sale),
						'amount'                => $point->value,
						'expired_at'			=> date('Y-m-d H:i:s', strtotime($sale->transact_at.' '.$expired->value)),
						'notes'					=> 'Bonus belanja '.$sale->customer->name
					]);

				if(!$pointlog->save())
				{
					$this->errors->add('Sale', $pointlog->getError());
				}
			}
		}
	}
}
