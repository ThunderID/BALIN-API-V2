<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Services\BalinRestock;
use App\Services\BalinRollbackStock;

/**
 * Handle Protected Resource of Purchase
 * 
 * @author cmooy
 */
class PurchaseController extends Controller
{
	public function __construct(Request $request, BalinRestock $restock_product, BalinRollbackStock $rollback_product)
	{
		$this->request 				= $request;
		$this->restock_product		= $restock_product;
		$this->rollback_product		= $rollback_product;
	}
	
	/**
	 * Display all purchases
	 *
	 * @param search, skip, take
	 * @return Response
	 */
	public function index()
	{
		$result                 = new \App\Entities\Purchase;

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
					return response()->json( JSend::error([$key.' harus bernilai asc atau desc.'])->asArray());
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

		$result                     = $result->with(['supplier'])->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Display a purchase
	 *
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Entities\Purchase::id($id)->with(['transactionlogs', 'supplier', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product'])->first();

		if($result)
		{
			return response()->json( JSend::success($result->toArray())->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::error(['ID Tidak Valid.'])->asArray());
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
			return response()->json( JSend::error(['Tidak ada data purchase.'])->asArray());
		}

		$purchase                    = Input::get('purchase');

		$this->restock_product->fill($purchase);

		if(!$this->restock_product->save())
		{
			return response()->json( JSend::error($this->restock_product->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($this->restock_product->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Delete a purchase
	 *
	 * @return Response
	 */
	public function delete($id = null)
	{
		//
		$purchase                   = \App\Entities\Purchase::id($id)->with(['transactionlogs', 'supplier', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product'])->first();

		if(!$purchase)
		{
			return response()->json( JSend::error(['Pembelian tidak ditemukan.'])->asArray());
		}

		if($this->rollback_product->delete($purchase))
		{
			return response()->json( JSend::success($this->rollback_product->getData())->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::error($this->delete_product->getError())->asArray());
	}
}
