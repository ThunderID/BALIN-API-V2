<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

/**
 * Handle Protected reports
 * 
 * @author cmooy
 */
class ReportController extends Controller
{
	public function __construct(Request $request)
	{
		$this->request 				= $request;
	}

	/**
	 * Display usage of voucher in transaction
	 *
	 * @param skip, take
	 * @return Response
	 */
	public function voucher()
	{
		$result                     = new \App\Entities\Sale;

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
					case 'amount':
						$result->sort 			= 'amount';
						$result->sort_param 	= $value;
						break;
					case 'newest':
						$result->sort 			= 'transact_at';
						$result->sort_param 	= $value;
						break;
					default:
						# code...
						break;
				}
			}
		}

		$result 					= $result->status(['paid', 'packed', 'shipping', 'delivered']);

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'ondate':
						$result 	= $result->TransactionLogChangedAt($value);
						break;
					case 'username':
						$result 	= $result->UserName($value);
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

		$result                     = $result->with(['user', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'payment', 'paidpointlogs', 'paidpointlogs.referencepointvoucher', 'paidpointlogs.referencepointvoucher.referencevoucher', 'paidpointlogs.referencepointreferral', 'paidpointlogs.referencepointreferral.referencereferral', 'paidpointlogs.pointlog'])->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}
	
	/**
	 * Display selled product
	 *
	 * @param skip, take
	 * @return Response
	 */
	public function product()
	{
		$result                     = new \App\Entities\Varian;

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'ondate':
						$result 	= $result->TransactionLogChangedAt($value);
						break;
					default:
						# code...
						break;
				}
			}
		}

		$count                      = count($result->havingsolditem(0)->get(['id']));

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

		$result                     = $result->havingsolditem(0)->with(['product'])->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}
}
