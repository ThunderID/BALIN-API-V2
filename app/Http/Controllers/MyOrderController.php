<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Services\BalinCheckout;
use App\Services\BankTransferHandlingPayment;
use App\Services\BalinPackingOrder;
use App\Services\BalinShippingOrder;
use App\Services\BalinDeliveredOrder;
use App\Services\BalinCancelOrder;
use App\Services\BalinAddToCart;
use Request, Carbon\Carbon;

class MyOrderController extends Controller
{
	public function __construct(Request $request, BalinCheckout $balincheckout, BankTransferHandlingPayment $balinpaid, BalinPackingOrder $balinpack, BalinShippingOrder $balinship, BalinDeliveredOrder $balindeliver, BalinCancelOrder $balincancel, BalinAddToCart $balincart)
	{
		$this->request 				= $request;
		$this->balincheckout		= $balincheckout;
		$this->balinpaid			= $balinpaid;
		$this->balinpack			= $balinpack;
		$this->balinship			= $balinship;
		$this->balindeliver			= $balindeliver;
		$this->balincancel			= $balincancel;
		$this->balincart			= $balincart;
	}
	
	/**
	 * Display all customer's recorded orders
	 *
	 * @return Response
	 */
	public function index($user_id = null)
	{
		$result                     = \App\Entities\Sale::userid($user_id)->status(['wait', 'canceled', 'payment_process', 'paid', 'shipping', 'packed', 'delivered']);

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
		$result                 = \App\Entities\Sale::userid($user_id)->id($order_id)->status(['wait', 'payment_process', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->with(['payment', 'orderlogs', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'shipment', 'shipment.courier', 'shipment.address', 'voucher', 'customer', 'transactionextensions', 'transactionextensions.productextension'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return response()->json( JSend::fail(['ID Tidak Valid.']));
	}

	/**
	 * Display an order by ref_number
	 *
	 * @return Response
	 */
	public function refnumber($user_id = null, $refnumber = null)
	{
		$result                 = \App\Entities\Sale::userid($user_id)->refnumber($refnumber)->status(['wait', 'payment_process', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->with(['payment', 'orderlogs', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'shipment', 'shipment.courier', 'shipment.address', 'voucher', 'customer', 'transactionextensions', 'transactionextensions.productextension'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return response()->json( JSend::fail(['ID Tidak Valid.']));
	}

	/**
	 * Display an order from customer's cart
	 *
	 * @return Response
	 */
	public function incart($user_id = null)
	{
		$result                 = \App\Entities\Sale::userid($user_id)->status('cart')->with(['payment', 'orderlogs', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'shipment', 'shipment.courier', 'shipment.address', 'voucher', 'customer', 'transactionextensions', 'transactionextensions.productextension'])->first();

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

		$sale                      	= Input::get('order');
		
		switch ($sale['status']) 
		{
			case 'wait':
				$sale_store			= $this->balincheckout;
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

		return response()->json( JSend::success($sale_store->getData()->toArray())->asArray());
	}
}
