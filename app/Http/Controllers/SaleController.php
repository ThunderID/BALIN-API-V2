<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of Sale
 * 
 * @author cmooy
 */
class SaleController extends Controller
{
	/**
	 * Display all sales
	 *
	 * @param search, skip, take
	 * @return Response
	 */
	public function index()
	{
		$result                 = new \App\Models\Sale;

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'expiredcart':
						$policy 	= new \App\Models\Policy;
						$policy 	= $policy->default(true)->type('expired_cart')->first();
						
						if($policy)
						{
							$result	= $result->status('cart')->TransactionLogChangedAt($policy['value']);
						}
						else
						{
							$result	= $result->status('cart')->TransactionLogChangedAt('- 2 days');
						}
						break;
					case 'expiredwait':
						$policy 	= new \App\Models\Policy;
						$policy 	= $policy->default(true)->type('expired_paid')->first();
						
						if($policy)
						{
							$result	= $result->status('wait')->TransactionLogChangedAt($policy['value']);
						}
						else
						{
							$result	= $result->status('wait')->TransactionLogChangedAt('- 2 days');
						}
						break;
					case 'ondate':
						$result 	= $result->TransactionLogChangedAt($value);
						break;
					case 'productnotes':
						$result 	= $result->ProductNotes(true);
						break;
					case 'addressnotes':
						$result 	= $result->AddressNotes(true);
						break;
					case 'shippingnotes':
						$result 	= $result->ShippingNotes(true)->with(['transactionextensions', 'transactionextensions.productextension']);
						break;
					case 'bills':
						$result 	= $result->bills($value);
						break;
					case 'status':
						$result 	= $result->status($value);
						break;
					case 'userid':
						$result 	= $result->userid($value);
						break;
					case 'refnumber':
						$result 	= $result->refnumber($value);
						break;
					default:
						# code...
						break;
				}
			}
		}

		if(Input::has('sort'))
		{
			$sort                 = Input::get('sort');

			foreach ($sort as $key => $value) 
			{
				if(!in_array($value, ['asc', 'desc']))
				{
					return new JSend('error', (array)Input::all(), $key.' harus bernilai asc atau desc.');
				}
				switch (strtolower($key)) 
				{
					case 'refnumber':
						$result     = $result->orderby('ref_number', $value);
						break;
					case 'bills':
						$result     = $result->orderby($key, $value);
						break;
					case 'newest':
						$result     = $result->orderby('transact_at', $value);
						break;
					default:
						# code...
						break;
				}
			}
		}

		$count                      = count($result->get(['id']));

		if(Input::has('skip'))
		{
			$skip                   = Input::get('skip');
			$result                 = $result->skip($skip);
		}

		if(Input::has('take'))
		{
			$take                   = Input::get('take');
			$result                 = $result->take($take);
		}

		$result                     = $result->with(['user'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display a sale
	 *
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Models\Sale::id($id)->with(['voucher', 'transactionlogs', 'user', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'paidpointlogs', 'payment', 'shipment', 'shipment.address', 'shipment.courier', 'transactionextensions', 'transactionextensions.productextension'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * Store a sale
	 *
	 * 1. Save Sale
	 * 2. Save Payment or shipment
	 * 3. Save Transaction Log
	 * 
	 * @return Response
	 */
	public function status()
	{
		if(!Input::has('sale'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data sale.');
		}

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Validate Sale Parameter
		$sale                       = Input::get('sale');

		if(is_null($sale['id']))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data sale.');
		}
		else
		{
			$is_new                 = false;
		}


		//1a. Get original data
		$sale_data              = \App\Models\Sale::findorfail($sale['id']);

		//1b. Check if there were statuses differences
		if($sale_data['status']==$sale['status'])
		{
			$errors->add('Sale', 'Tidak ada perubahan status.');
		}

		//2. Check if status = paid
		if(!$errors->count() && in_array($sale['status'], ['paid']))
		{
			if(!isset($sale['payment']) || is_null($sale['payment']))
			{
				$errors->add('Sale', 'Tidak ada data pembayaran.');
			}
			else
			{
				$paid_data		= \App\Models\Payment::findornew($sale['payment']['id']);
				
				$payment_rule   =   [
										'transaction_id'	=> 'exists:transactions,id|'.(!$paid_data ? '' : 'in:'.$sale_data['id']),
										'method'			=> 'required|max:255',
										'destination'		=> 'required|max:255',
										'account_name'		=> 'required|max:255',
										'account_number'	=> 'required|max:255',
										'ondate'			=> 'required|date_format:"Y-m-d H:i:s"',
										'amount'			=> 'required|numeric|in:'.$sale_data['bills'],
									];

				$validator   = Validator::make($sale['payment'], $payment_rule);

				//if there was log and validator false
				if (!$validator->passes())
				{
					$errors->add('Log', $validator->errors());
				}
				else
				{
					$sale['payment']['transaction_id']	= $sale['id'];
					$paid_data                    		= $paid_data->fill($sale['payment']);

					if(!$paid_data->save())
					{
						$errors->add('Log', $paid_data->getError());
					}
				}
			}
		}

		//2. Check if status = shipping
		if(!$errors->count() && in_array($sale['status'], ['shipping']))
		{
			if(is_null($sale['shipment']['receipt_number']))
			{
				$errors->add('Sale', 'Tidak ada nomor resi.');
			}
			else
			{
				$shipping_data       = \App\Models\Shipment::id($sale['shipment']['id'])->first();

				if($shipping_data)
				{
					$shipment_rule   =  [
											'receipt_number'            => 'required|max:255',
										];

					$validator		= Validator::make($sale['shipment'], $shipment_rule);

					//if there was log and validator false
					if (!$validator->passes())
					{
						$errors->add('Log', $validator->errors());
					}
					else
					{
						$sale['shipment']['transaction_id']	= $sale['id'];
						$shipping_data                    	= $shipping_data->fill($sale['shipment']);

						if(!$shipping_data->save())
						{
							$errors->add('Log', $shipping_data->getError());
						}
					}
				}
				else
				{
					$errors->add('Log', 'Shipment tidak valid.');
				}
			}
		}

		//3. Check if status = others
		if(!$errors->count() && !in_array($sale['status'], ['paid', 'shipping']))
		{
			$log_rules   =   [
									'transaction_id'	=> 'exists:transactions,id|'.($is_new ? '' : 'in:'.$sale_data['id']),
									'status'			=> 'required|max:255|in:cart,wait,payment_process,paid,packed,shipping,delivered,canceled,abandoned',
								];

			$validator   = Validator::make($sale, $log_rules);

			//if there was log and validator false
			if (!$validator->passes())
			{
				$errors->add('Log', $validator->errors());
			}
			else
			{
				$log_data                    = new \App\Models\TransactionLog;

				$log_data                    = $log_data->fill(['status' => $sale['status'], 'transaction_id' => $sale_data['id'], 'notes' => (isset($sale['notes']) ? $sale['notes'] : '')]);

				if(!$log_data->save())
				{
					$errors->add('Log', $log_data->getError());
				}
			}
		}


		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_sale                 = \App\Models\Sale::id($sale_data['id'])->with(['voucher', 'transactionlogs', 'user', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'paidpointlogs', 'payment', 'shipment', 'shipment.address', 'shipment.courier', 'transactionextensions', 'transactionextensions.productextension'])->first()->toArray();

		return new JSend('success', (array)$final_sale);
	}
}
