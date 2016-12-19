<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Services\BalinStoreVoucher;
use App\Services\BalinDeleteVoucher;
/**
 * Handle Protected Resource of voucher
 * 
 * @author cmooy
 */
class VoucherController extends Controller
{
	public function __construct(Request $request, BalinStoreVoucher $store_voucher, BalinDeleteVoucher $delete_voucher)
	{
		$this->request 				= $request;
		$this->store_voucher		= $store_voucher;
		$this->delete_voucher		= $delete_voucher;
	}
	
	/**
	 * Display all vouchers
	 *
	 * @param search, skip, take
	 * @return Response
	 */
	public function index()
	{
		$result						= new \App\Entities\Voucher;

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
					return response()->json( JSend::error([$key.' harus bernilai asc atau desc.'])->asArray());
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

		$result                     = $result->with(['quotalogs'])->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Display a voucher
	 *
	 * @param voucher id
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Entities\Voucher::id($id)->with(['quotalogs', 'sales', 'sales.customer'])->first();

		if($result)
		{
			return response()->json( JSend::success($result->toArray())->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::error(['ID Tidak Valid.'])->asArray());
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
			return response()->json( JSend::error(['Tidak ada data voucher.'])->asArray());
		}

		//1. Validate Voucher Parameter
		$voucher                    = Input::get('voucher');

		$this->store_voucher->fill($voucher);

		if(!$this->store_voucher->save())
		{
			return response()->json( JSend::error($this->store_voucher->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($this->store_voucher->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
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
		$voucher                    = \App\Entities\Voucher::id($id)->with(['quotalogs', 'transactions'])->first();

		if(!$voucher)
		{
			return response()->json( JSend::error(['Voucher tidak ditemukan.'])->asArray());
		}

		if($this->delete_voucher->delete($voucher))
		{
			return response()->json( JSend::success($this->delete_voucher->getData())->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::error($this->delete_voucher->getError())->asArray());
	}
}
