<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Request, Carbon\Carbon;

class MyOrderController extends Controller
{
	/**
	 * Display all customer's recorded orders
	 *
	 * @return Response
	 */
	public function index($user_id = null)
	{
		$result                     = \App\Models\Sale::userid($user_id)->status(['wait', 'canceled', 'payment_process', 'paid', 'shipping', 'packed', 'delivered']);

		$count                      = count($result->get());

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

		$result                     = $result->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display an order by customer
	 *
	 * @return Response
	 */
	public function detail($user_id = null, $order_id = null)
	{
		$result                 = \App\Models\Sale::userid($user_id)->id($order_id)->status(['wait', 'payment_process', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->with(['payment', 'orderlogs', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'shipment', 'shipment.courier', 'shipment.address', 'voucher', 'user', 'transactionextensions', 'transactionextensions.productextension'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * Display an order by ref_number
	 *
	 * @return Response
	 */
	public function refnumber($user_id = null, $refnumber = null)
	{
		$result                 = \App\Models\Sale::userid($user_id)->refnumber($refnumber)->status(['wait', 'payment_process', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->with(['payment', 'orderlogs', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'shipment', 'shipment.courier', 'shipment.address', 'voucher', 'user', 'transactionextensions', 'transactionextensions.productextension'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * Display an order from customer's cart
	 *
	 * @return Response
	 */
	public function incart($user_id = null)
	{
		$result                 = \App\Models\Sale::userid($user_id)->status('cart')->with(['payment', 'orderlogs', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'shipment', 'shipment.courier', 'shipment.address', 'voucher', 'user', 'transactionextensions', 'transactionextensions.productextension'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}
		
		return new JSend('error', (array)Input::all(), 'Tidak ada cart.');
	}


	/**
	 * Display store customer order
	 *
	 * @return Response
	 */
	public function store()
	{
		if(!Input::has('order'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data order.');
		}

		$errors                     = new MessageBag();

		$order                      = Input::get('order');

		DB::beginTransaction();

		$user_id                    = Request::route()[2]['user_id'];

		$order                      = Input::get('order');

		if(is_null($order['id']))
		{
			$is_new                 = true;
		}
		else
		{
			$is_new                 = false;
		}

		if(isset($order['voucher_code']))
		{
			//a. check if voucher code is voucher
			$voucher 						= \App\Models\Voucher::code($order['voucher_code'])->type(['free_shipping_cost', 'debit_point'])->first();

			if(!$voucher)
			{
				//b. check if voucher is referral
				$voucher_data              	= \App\Models\Referral::code($order['voucher_code'])->first();
				if(!$voucher_data)
				{
					$voucher_data			= \App\Models\Voucher::code($order['voucher_code'])->type('promo_referral')->ondate('now')->first();
				}

				if(!$voucher_data)
				{
					return new JSend('error', (array)Input::all(), ['Voucher tidak valid.']);
				}
				elseif($voucher_data->quota <= 0)
				{
					$errors->add('Redeem', 'Quota referral sudah habis.');
				}
				else
				{
					$store                      = \App\Models\StoreSetting::type('voucher_point_expired')->Ondate('now')->first();

					if($store)
					{
						$expired_at             = new Carbon($store->value);
					}
					else
					{
						$expired_at             = new Carbon('+ 3 months');
					}

					//if validator passed, save voucher
					if($voucher_data['type']=='referral')
					{
						$reference_id 			= $voucher_data['user_id'];
						$reference_type			= 'App\Models\User';
					}
					else
					{
						$reference_id 			= $voucher_data['id'];
						$reference_type			= 'App\Models\Voucher';
					}

					$point                  =   [
													'user_id'               => $user_id,
													'reference_id'        	=> $reference_id,
													'reference_type'        => $reference_type,
													'expired_at'            => $expired_at->format('Y-m-d H:i:s'),
												];

					$point_data             = new \App\Models\PointLog;
					
					$point_data->fill($point);

					if(!$point_data->save())
					{
						$errors->add('Redeem', $point_data->getError());
					}
				}
			}
			else
			{
				$order['voucher_id']	= $voucher['id'];
			}

		}

		if(isset($order['voucher_id']) && ($order['voucher_id'])==0)
		{
			$order['voucher_id']	= '';
		}

		$order_rules                =   [
											// 'user_id'                   => 'required|exists:users,id',
											'voucher_id'                => 'exists:tmp_vouchers,id',
										];

		//1a. Get original data
		$order_data					= \App\Models\Sale::findornew($order['id']);

		//1b. Validate Basic Order Parameter
		$validator					= Validator::make($order, $order_rules);

		if (!$validator->passes() && !$errors->count())
		{
			$errors->add('Sale', $validator->errors());
		}
		elseif (!$errors->count())
		{
			//if validator passed, save order
			$order_data           = $order_data->fill(['user_id' => $user_id, 'voucher_id' => (isset($order['voucher_id']) ? $order['voucher_id'] : '0')]);

			if(!$order_data->save())
			{
				$errors->add('Sale', $order_data->getError());
			}
		}

		//2. Validate Order Detail Parameter
		if(!$errors->count() && isset($order['transactiondetails']) && is_array($order['transactiondetails']))
		{
			$detail_current_ids         = [];
			foreach ($order['transactiondetails'] as $key => $value) 
			{
				if(!$errors->count() && isset($value['quantity']) && $value['quantity']>0)
				{
					$detail_data		= \App\Models\TransactionDetail::findornew($value['id']);

					$detail_rules		=   [
												'transaction_id'            => 'exists:transactions,id|'.($is_new ? '' : 'in:'.$order_data['id']),
												'varian_id'                 => 'required|exists:varians,id',
												'quantity'                  => 'required|numeric',
												'price'                     => 'required|numeric',
												'discount'                  => 'numeric',
											];

					$validator			= Validator::make($value, $detail_rules);

					//if there was detail and validator false
					if (!$validator->passes())
					{
						$errors->add('Detail', $validator->errors());
					}
					else
					{
						$check_prev_trans 				= \App\Models\TransactionDetail::transactionid($order_data['id'])->varianid($value['varian_id'])->first();
						if($check_prev_trans)
						{
							$detail_data 				= $check_prev_trans;
						}
						
						$value['transaction_id']        = $order_data['id'];

						$detail_data                    = $detail_data->fill($value);

						if(!$detail_data->save())
						{
							$errors->add('Detail', $detail_data->getError());
						}
						else
						{
							$detail_current_ids[]       = $detail_data['id'];
						}
					}
				}
			}

			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$details                            = \App\Models\TransactionDetail::transactionid($order['id'])->get(['id'])->toArray();
				
				$detail_should_be_ids               = [];
				foreach ($details as $key => $value) 
				{
					$detail_should_be_ids[]         = $value['id'];
				}

				$difference_detail_ids              = array_diff($detail_should_be_ids, $detail_current_ids);

				if($difference_detail_ids)
				{
					foreach ($difference_detail_ids as $key => $value) 
					{
						$detail_data                = \App\Models\TransactionDetail::find($value);

						if(!$detail_data->delete())
						{
							$errors->add('Detail', $detail_data->getError());
						}
					}
				}
			}
		}

		//3. Validate Order Detail Parameter
		if(!$errors->count() && isset($order['transactionextensions']) && is_array($order['transactionextensions']))
		{
			$extend_current_ids         = [];
			foreach ($order['transactionextensions'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$extend_data		= \App\Models\TransactionExtension::findornew($value['id']);

					$extend_rules		=   [
												'transaction_id'            => 'exists:transactions,id|'.($is_new ? '' : 'in:'.$order_data['id']),
												'product_extension_id'		=> 'required|exists:product_extensions,id',
												'price'                     => 'required|numeric',
											];

					$validator			= Validator::make($value, $extend_rules);

					//if there was extend and validator false
					if (!$validator->passes())
					{
						$errors->add('Extend', $validator->errors());
					}
					else
					{
						$check_prev_trans 				= \App\Models\TransactionExtension::transactionid($order_data['id'])->productextensionid($value['product_extension_id'])->first();
						if($check_prev_trans)
						{
							$extend_data 				= $check_prev_trans;
						}
						
						$value['transaction_id']        = $order_data['id'];

						$extend_data                    = $extend_data->fill($value);

						if(!$extend_data->save())
						{
							$errors->add('Extend', $extend_data->getError());
						}
						else
						{
							$extend_current_ids[]       = $extend_data['id'];
						}
					}
				}
			}

			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$extends                            = \App\Models\TransactionExtension::transactionid($order['id'])->get(['id'])->toArray();
				
				$extend_should_be_ids               = [];
				foreach ($extends as $key => $value) 
				{
					$extend_should_be_ids[]         = $value['id'];
				}

				$difference_extend_ids              = array_diff($extend_should_be_ids, $extend_current_ids);

				if($difference_extend_ids)
				{
					foreach ($difference_extend_ids as $key => $value) 
					{
						$extend_data                = \App\Models\TransactionExtension::find($value);

						if(!$extend_data->delete())
						{
							$errors->add('Extend', $extend_data->getError());
						}
					}
				}
			}
		}

		//4. Check if need to save address
		if(!$errors->count() && isset($order['shipment']['address']))
		{
			$address_data        = \App\Models\Address::findornew($order['shipment']['address']['id']);

			$address_rules		=   [
										'owner_id'		=> 'exists:users,id|'.($is_new ? '' : 'in:'.$user_id),
										'owner_type'	=> ($is_new ? '' : 'in:App\Models\Customer'),
										'phone'			=> 'required|max:255',
										'address'		=> 'required',
										'zipcode'		=> 'required|max:255',
									];

			$validator      	= Validator::make($order['shipment']['address'], $address_rules);

			//4a. save address
			//if there was address and validator false
			if (!$validator->passes())
			{
				$errors->add('Sale', $validator->errors());
			}
			else
			{
				//if validator passed, save address
				$order['shipment']['address']['owner_id']			= $user_id;
				$order['shipment']['address']['owner_type']			= 'App\Models\Customer';

				$address_data										= $address_data->fill($order['shipment']['address']);

				if(!$address_data->save())
				{
					$errors->add('Sale', $address_data->getError());
				}
			}
		}

		//4b. save shipment
		if(!$errors->count() && isset($order['shipment']))
		{
			if($order_data->shipment()->count())
			{
				$shipment_data      = \App\Models\Shipment::findorfail($order_data->shipment->id);
			}
			else
			{
				$shipment_data      = \App\Models\Shipment::findornew($order['shipment']['id']);
			}

			$shipment_rules     	=   [
											'courier_id'	=> 'required|exists:couriers,id',
											'receiver_name'	=> 'required|max:255',
										];

			$validator				= Validator::make($order['shipment'], $shipment_rules);

			//4c. save shipment
			//if there was shipment and validator false
			if (!$validator->passes())
			{
				$errors->add('Sale', $validator->errors());
			}
			else
			{
				//if validator passed, save shipment
				$order['shipment']['transaction_id']    = $order['id'];
				if(isset($address_data['id']))
				{
					$order['shipment']['address_id']	= $address_data['id'];
				}

				$shipment_data       = $shipment_data->fill($order['shipment']);

				if(!$shipment_data->save())
				{
					$errors->add('Sale', $shipment_data->getError());
				}
			}
		}

		//5. update status
		if(!$errors->count() && isset($order['status']) && $order_data['status'] != $order['status'])
		{
			//3a. check cart price and product current  price
			if($order['status']=='wait')
			{
				foreach ($order_data['transactiondetails'] as $key => $value) 
				{
					$discount 			= ($value['varian']['product']['promo_price']==0 ? (0) : ($value['varian']['product']['price'] - $value['varian']['product']['promo_price']));
					if($value['price'] != $value['varian']['product']['price'] || $value['discount'] != $discount )
					{
						$errors->add('Price', 'Harga item '. $value['varian']['product']['name'].' telah berubah sejak '.$value['varian']['product']['price_start'].'. Silahkan update keranjang Anda.');
					}
				}

				foreach ($order_data['transactionextensions'] as $key => $value) 
				{
					if($value['price'] != $value['productextension']['price'])
					{
						$errors->add('Price', 'Biaya '. $value['productextension']['name'].' telah berubah sejak '.$value['productextension']['updated_at'].'. Silahkan update keranjang Anda.');
					}

					if(!$value['productextension']['is_active'])
					{
						$errors->add('Active', $value['productextension']['name'].' telah di non aktif kan sejak '.$value['productextension']['updated_at'].'. Silahkan update keranjang Anda.');
					}
				}
			}

			if(!$errors->count())
			{
				$log_data			= new \App\Models\TransactionLog;

				$log_data			= $log_data->fill(['status' => $order['status'], 'transaction_id' => $order_data['id']]);

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
		
		$final_order                 = \App\Models\Sale::userid($user_id)->id($order_data['id'])->status(['cart', 'wait', 'payment_process', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->with(['payment', 'orderlogs', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'shipment', 'shipment.courier', 'shipment.address', 'voucher', 'user', 'transactionextensions', 'transactionextensions.productextension'])->first()->toArray();

		return new JSend('success', (array)$final_order);
	}
}
