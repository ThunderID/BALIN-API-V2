<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Services\BalinCheckout;
use App\Services\BankTransferHandlingPayment;
use App\Services\BalinPackingOrder;
use App\Services\BalinShippingOrder;
use App\Services\BalinDeliveredOrder;
use App\Services\BalinCancelOrder;
use App\Services\BalinAddToCart;
use App\Services\ThirdPartyCheckout;
/**
 * Handle Protected Resource of Sale
 * 
 * @author cmooy
 */
class SaleController extends Controller
{
	public function __construct(Request $request, BalinCheckout $balincheckout, BankTransferHandlingPayment $balinpaid, BalinPackingOrder $balinpack, BalinShippingOrder $balinship, BalinDeliveredOrder $balindeliver, BalinCancelOrder $balincancel, BalinAddToCart $balincart, ThirdPartyCheckout $thirdparty)
	{
		$this->request 				= $request;
		$this->balincheckout		= $balincheckout;
		$this->balinpaid			= $balinpaid;
		$this->balinpack			= $balinpack;
		$this->balinship			= $balinship;
		$this->balindeliver			= $balindeliver;
		$this->balincancel			= $balincancel;
		$this->balincart			= $balincart;
		$this->thirdparty			= $thirdparty;
	}

	/**
	 * Display all sales
	 *
	 * @param search, skip, take
	 * @return Response
	 */
	public function index()
	{
		$result                 = new \App\Entities\Sale;

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'expiredcart':
						$policy 	= new \App\Entities\Policy;
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
						$policy 	= new \App\Entities\Policy;
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
					return response()->json( JSend::error([$key.' harus bernilai asc atau desc.'])->asArray());
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

		$result                     = $result->with(['user'])->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Display a sale
	 *
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Entities\Sale::id($id)->with(['voucher', 'transactionlogs', 'user', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'paidpointlogs', 'payment', 'shipment', 'shipment.address', 'shipment.courier', 'transactionextensions', 'transactionextensions.productextension'])->first();

		if($result)
		{
			return response()->json( JSend::success($result->toArray())->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::error(['ID Tidak Valid.'])->asArray());
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
			return response()->json( JSend::error(['Tidak ada data sale.'])->asArray());
		}

		//1. Validate Sale Parameter
		$sale						= Input::get('sale');

		if(is_null($sale['id']))
		{
			return response()->json( JSend::error(['Tidak ada data sale.'])->asArray());
		}

		switch ($sale['status']) 
		{
			case 'wait':
				$sale_store			= $this->balincheckout;
				break;
			case 'paid':
				$sale_store			= $this->balinpaid;
				break;
			case 'packed':
				$sale_store			= $this->balinpack;
				break;
			case 'shipping':
				$sale_store			= $this->balinship;
				break;
			case 'delivered':
				$sale_store			= $this->balindeliver;
				break;
			case 'canceled':
				$sale_store			= $this->balincancel;
				break;
			default:
				$sale_store			= $this->balincart;
				break;
		}

		$sale_store->fill($sale);

		if(!$sale_store->save())
		{
			return response()->json( JSend::error($sale_store->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($sale_store->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Store a third party
	 *
	 * @return Response
	 */
	public function thirdparty()
	{
		if(!Input::has('sale'))
		{
			return response()->json( JSend::error(['Tidak ada data sale.'])->asArray());
		}

		//1. Validate Sale Parameter
		$sale				= Input::get('sale');

		$sale_store			= $this->thirdparty;

		$sale_store->fill($sale);

		if(!$sale_store->save())
		{
			return response()->json( JSend::error($sale_store->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($sale_store->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}
}
