<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of voucher
 * 
 * @author cmooy
 */
class VoucherController extends Controller
{
	/**
	 * Display all vouchers
	 *
	 * @param search, skip, take
	 * @return Response
	 */
	public function index()
	{
		$result						= new \App\Models\Voucher;

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'code':
						$result     = $result->code($value);
						break;
					case 'type':
						$result     = $result->type($value);
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
					case 'code':
						$result     = $result->orderby($key, $value);
						break;
					case 'newest':
						$result     = $result->orderby('started_at', $value);
						break;
					case 'amount':
						$result     = $result->orderby('value', $value);
						break;
					case 'quota':
						$result     = $result->orderby($key, $value);
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

		$result                     = $result->with(['quotalogs'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display a voucher
	 *
	 * @param voucher id
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Models\Voucher::id($id)->with(['quotalogs', 'sales', 'sales.customer'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}
		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * Store a Voucher
	 *
	 * 1. Save Vouchers
	 * 2. Save Quota Logs
	 *
	 * @return Response
	 */
	public function store()
	{
		if(!Input::has('voucher'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data voucher.');
		}

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Validate Voucher Parameter
		$voucher                    = Input::get('voucher');
		
		if(is_null($voucher['id']))
		{
			$is_new                 = true;
		}
		else
		{
			$is_new                 = false;
		}

		$voucher_rules             =   [
											'user_id'		=> 'exists:users,id',
											'code'			=> 'required|max:255|unique:tmp_vouchers,code,'.(!is_null($voucher['id']) ? $voucher['id'] : ''),
											'type'			=> 'required|in:debit_point,free_shipping_cost,promo_referral',
											'value'			=> 'required|numeric',
											'started_at'	=> 'date_format:"Y-m-d H:i:s"',
											'expired_at'	=> 'date_format:"Y-m-d H:i:s"',
										];

		//1a. Get original data
		$voucher_data              = \App\Models\Voucher::findornew($voucher['id']);

		//1b. Validate Basic Voucher Parameter
		$validator                  = Validator::make($voucher, $voucher_rules);

		if (!$validator->passes())
		{
			$errors->add('Voucher', $validator->errors());
		}
		else
		{
			//if validator passed, save voucher
			$voucher_data           = $voucher_data->fill($voucher);

			if(!$voucher_data->save())
			{
				$errors->add('Voucher', $voucher_data->getError());
			}
		}

		//2. Validate Voucher Logs Parameter
		//2a. save using quota
		if(!$errors->count() && isset($voucher['quota']) && $voucher['quota'] != $voucher_data['quota'])
		{
			$log_data        = new \App\Models\QuotaLog;

			$value['voucher_id']        = $voucher_data['id'];
			$value['amount']			= $voucher['quota'] - $voucher_data['quota'];

			$log_data                    = $log_data->fill($value);

			if(!$log_data->save())
			{
				$errors->add('Log', $log_data->getError());
			}
		}
		
		if(!$errors->count() && isset($voucher['quotalogs']) && is_array($voucher['quotalogs']))
		{
			$log_current_ids         = [];
			foreach ($voucher['quotalogs'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$log_data        = \App\Models\QuotaLog::findornew($value['id']);

					$log_rules		=   [
											'voucher_id'	=> 'exists:tmp_vouchers,id|'.($is_new ? '' : 'in:'.$voucher_data['id']),
											'amount'		=> 'required|numeric',
											'notes'			=> 'max:512',
										];

					$validator      = Validator::make($value, $log_rules);

					//if there was log and validator false
					if (!$validator->passes())
					{
						$errors->add('Log', $validator->errors());
					}
					else
					{
						$value['voucher_id']        = $voucher_data['id'];

						$log_data                    = $log_data->fill($value);

						if(!$log_data->save())
						{
							$errors->add('Log', $log_data->getError());
						}
						else
						{
							$log_current_ids[]       = $log_data['id'];
						}
					}
				}
			}

			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$logs                            = \App\Models\QuotaLog::voucherid($voucher['id'])->get(['id'])->toArray();
				
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
						$log_data                = \App\Models\QuotaLog::find($value);

						if(!$log_data->delete())
						{
							$errors->add('Log', $log_data->getError());
						}
					}
				}
			}
		}

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_voucher                 = \App\Models\Voucher::id($voucher_data['id'])->with(['quotalogs', 'transactions'])->first()->toArray();

		return new JSend('success', (array)$final_voucher);
	}

	/**
	 * Delete a voucher
	 *
	 * @param product id
	 * @return Response
	 */
	public function delete($id = null)
	{
		//
		$voucher                    = \App\Models\Voucher::id($id)->with(['quotalogs', 'transactions'])->first();

		if(!$voucher)
		{
			return new JSend('error', (array)Input::all(), 'Produk tidak ditemukan.');
		}

		$result                     = $voucher->toArray();

		if($voucher->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $voucher->getError());
	}
}
