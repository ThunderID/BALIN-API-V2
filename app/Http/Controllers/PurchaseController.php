<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of Purchase
 * 
 * @author cmooy
 */
class PurchaseController extends Controller
{
	/**
	 * Display all purchases
	 *
	 * @param search, skip, take
	 * @return Response
	 */
	public function index()
	{
		$result                 = new \App\Models\Purchase;

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'ondate':
						$result		= $result->TransactionLogChangedAt($value);
						break;
					case 'refnumber':
						$result		= $result->refnumber($value);
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
					case 'amount':
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

		$result                     = $result->with(['supplier'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display a purchase
	 *
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Models\Purchase::id($id)->with(['transactionlogs', 'supplier', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());

		}
		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * Store a purchase
	 *
	 * 1. Save Purchase
	 * 2. Save Transaction Detail
	 * 3. Save Transaction Log
	 * 
	 * @return Response
	 */
	public function store()
	{
		if(!Input::has('purchase'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data purchase.');
		}

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Validate Purchase Parameter
		
		$purchase                    = Input::get('purchase');

		if(is_null($purchase['id']))
		{
			$is_new                 = true;
		}
		else
		{
			$is_new                 = false;
		}

		$purchase_rules             =   [
											'supplier_id'                   => 'required|exists:suppliers,id'
										];

		//1a. Get original data
		$purchase_data              = \App\Models\Purchase::findornew($purchase['id']);

		//1b. Validate Basic Purchase Parameter
		$validator                  = Validator::make($purchase, $purchase_rules);

		if (!$validator->passes())
		{
			$errors->add('Purchase', $validator->errors());
		}
		else
		{
			//if validator passed, save purchase
			$purchase_data           = $purchase_data->fill(['supplier_id' => $purchase['supplier_id'], 'type' => 'buy', 'transact_at' => isset($purchase['transact_at']) ? $purchase['transact_at'] : '']);

			if(!$purchase_data->save())
			{
				$errors->add('Purchase', $purchase_data->getError());
			}
		}

		//2. Validate Purchase Detail Parameter
		if(!$errors->count() && isset($purchase['transactiondetails']) && is_array($purchase['transactiondetails']))
		{
			$detail_current_ids         = [];
			foreach ($purchase['transactiondetails'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$detail_data        = \App\Models\TransactionDetail::findornew($value['id']);

					$detail_rules   	=   [
												'transaction_id'            => 'exists:transactions,id|'.($is_new ? '' : 'in:'.$purchase_data['id']),
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
						$value['transaction_id']        = $purchase_data['id'];

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
				$details                            = \App\Models\TransactionDetail::transactionid($purchase['id'])->get(['id'])->toArray();
				
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

		//3. Validate Purchase Status Parameter
		if(!$errors->count() && isset($purchase['transactionlogs']) && is_array($purchase['transactionlogs']))
		{
			$log_current_ids         = [];
			foreach ($purchase['transactionlogs'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$log_data		= \App\Models\TransactionLog::findornew($value['id']);

					$log_rules		=   [
											'transaction_id'            => 'exists:transactions,id|'.($is_new ? '' : 'in:'.$purchase_data['id']),
											'status'                    => 'required|max:255|in:cart,wait,paid,packed,shipping,delivered,canceled,abandoned',
										];

					$validator		= Validator::make($value, $log_rules);

					//if there was log and validator false
					if (!$validator->passes())
					{
						$errors->add('Log', $validator->errors());
					}
					else
					{
						$value['transaction_id']	= $purchase_data['id'];

						$log_data					= $log_data->fill($value);

						if(!$log_data->save())
						{
							$errors->add('Log', $log_data->getError());
						}
						else
						{
							$log_current_ids[]		= $log_data['id'];
						}
					}
				}
			}

			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$logs                            = \App\Models\TransactionLog::transactionid($purchase['id'])->get(['id'])->toArray();
				
				$log_should_be_ids               = [];
				foreach ($logs as $key => $value) 
				{
					$log_should_be_ids[]         = $value['id'];
				}

				$difference_log_ids              = array_diff($log_should_be_ids, $log_current_ids);

				if($difference_log_ids)
				{
					foreach ($difference_log_ids as $key => $value) 
					{
						$log_data                = \App\Models\TransactionLog::find($value);

						if(!$log_data->delete())
						{
							$errors->add('Log', $log_data->getError());
						}
					}
				}
			}
		}

		//4. Compare status
		if(isset($purchase['status']) && $purchase_data['status']!=$purchase['status'])
		{
			$log_rules   =   [
									'transaction_id'            => 'exists:transactions,id|'.($is_new ? '' : 'in:'.$purchase_data['id']),
									'status'                    => 'required|max:255|in:cart,wait,paid,packed,shipping,delivered,canceled,abandoned',
								];

			$validator   = Validator::make($purchase, $log_rules);

			//if there was log and validator false
			if (!$validator->passes())
			{
				$errors->add('Log', 'Status Tidak Valid.');
			}
			else
			{
				$log_data                    = new \App\Models\TransactionLog;

				$log_data                    = $log_data->fill(['status' => $purchase['status'], 'transaction_id' => $purchase_data['id']]);

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
		
		$final_purchase                 = \App\Models\Purchase::id($purchase_data['id'])->with(['transactionlogs', 'supplier', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product'])->first()->toArray();

		return new JSend('success', (array)$final_purchase);
	}

	/**
	 * Delete a purchase
	 *
	 * @return Response
	 */
	public function delete($id = null)
	{
		//
		$purchase                   = \App\Models\Purchase::id($id)->with(['transactionlogs', 'supplier', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product'])->first();

		$result                     = $purchase;

		if($purchase->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $purchase->getError());
	}
}
