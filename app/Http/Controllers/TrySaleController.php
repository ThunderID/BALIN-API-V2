<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;

use App\Http\Controllers\Veritrans\Veritrans_Config;
use App\Http\Controllers\Veritrans\Veritrans_Transaction;
use App\Http\Controllers\Veritrans\Veritrans_ApiRequestor;
use App\Http\Controllers\Veritrans\Veritrans_Notification;
use App\Http\Controllers\Veritrans\Veritrans_VtDirect;
use App\Http\Controllers\Veritrans\Veritrans_VtWeb;
use App\Http\Controllers\Veritrans\Veritrans_Sanitizer;

use App\Services\BalinShippingOrder as Checkout;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

use GenTux\Jwt\JwtToken;
use GenTux\Jwt\GetsJwtToken;

/**
 * Handle Protected Resource of Sale
 * 
 * @author cmooy
 */
class TrySaleController extends Controller
{
	use GetsJwtToken;

	public function __construct(Checkout $checkout, Request $request)
    {
        $this->class = $checkout;
        $this->request 	= $request;
    }

	/**
	 * Checkout
	 *
	 * 1. Call Class addtocart
	 * 
	 * @return Response
	 */
	public function addtocart(JwtToken $jwt)
	{

		// if( ! $this->jwtToken()->validate()) {
		// 	dd(9);
  //       }

  //       dd(10);
        
		// $user 	= \App\Entities\Auditor::first();
		// $user 	= new \App\Entities\ProductLabel;
		// dd($user);
		// dd($user->toArray());
		// $payload 	= 	[
		// 					'sub' => $user->id,
		// 				    'exp' => time() + 7200,
		// 				    'context' => [
		// 				        'email' => $user->email
		// 				    ]
		// 	            ];
		// $jwt 		= new JwtToken;

		$app 				= \App\Entities\Sale::status(['packed'])->with(['voucher', 'transactionlogs', 'user', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'paidpointlogs', 'payment', 'shipment', 'shipment.address', 'shipment.courier', 'transactionextensions', 'transactionextensions.productextension'])->wherehas('paidpointlogs', function($q){$q;})->take(1)->get()->toArray();
		// $app 				= \App\Models\Sale::id(78)->with(['voucher', 'transactionlogs', 'user', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'paidpointlogs', 'payment', 'shipment', 'shipment.address', 'shipment.courier', 'transactionextensions', 'transactionextensions.productextension'])->first()->toArray();
// dd($app);
// dd($app[0]);
// 		$modified = $app[0];
// 		$modified['id'] = null;
// 		$modified['transactiondetails'][0]['transaction_id'] = null;
// 		$modified['transactiondetails'][0]['id'] = null;
// 		$modified['shipment']['address']['id'] = null;
// 		$modified['user']['id'] = null;
// 		$modified['user']['email'] = 'cmooy@gmail.com';
// 		unset($modified['voucher']);
// 		unset($modified['paidpointlogs']);
// 		unset($modified['transactionextensions']);
// 		unset($modified['ref_number']);
// 		unset($modified['bills']);
// 		unset($modified['extend_cost']);
// 		unset($modified['user_id']);
// 		unset($modified['transact_at']);
// 		unset($modified['unique_number']);
// 		unset($modified['shipping_cost']);
// 		unset($modified['voucher_discount']);
// 		unset($modified['amount']);
// 		unset($modified['status']);
// 		unset($modified['transactionlogs']);
// 		unset($modified['shipment']['address_id']);
// 		unset($modified['shipment']['transaction_id']);
// 		unset($modified['shipment']['address']['owner_id']);
// 		unset($modified['shipment']['address']['owner_type']);
// 		unset($modified['shipment']['courier']);
// 		// Set our server key
// 		Veritrans_Config::$serverKey	= env('VERITRANS_KEY', 'VT_KEY');

// 		// Uncomment for production environment
// 		Veritrans_Config::$isProduction	= env('VERITRANS_PRODUCTION', false);

// 		$notif 							= new Veritrans_Notification(['transaction_id' => $app['ref_number']]);

// 		$transaction 					= $notif->transaction_status;

// 			$paid_data					= new \App\Models\Payment;

// 			$payment['id']				= null;
// 			$payment['transaction_id']	= $app['id'];
// 			$payment['method']			= 'deny';
// 			$payment['destination']		= 'Veritrans';
// 			$payment['account_name']	= $notif->masked_card;
// 			$payment['account_number']	= $notif->approval_code;
// 			$payment['ondate']			= \Carbon\Carbon::parse($notif->transaction_time)->format('Y-m-d H:i:s');
// 			$payment['amount']			= $notif->gross_amount;
// 			$payment['status']			= $transaction;
			// $payment['id']				= null;
			// $payment['transaction_id']	= $app[0]['id'];
			// $payment['destination']		= 'Tokopedia';
			// $payment['account_name']	= 'Agil';
			// $payment['account_number']	= '1234567890';
			// $payment['ondate']			= \Carbon\Carbon::now()->format('Y-m-d H:i:s');
			// $payment['amount']			= '642996';
			// $payment['method']			= 'transfer';

		// $app[0]['payment'] 	= $payment;
		// $modified['payment'] 	= $payment;

// 		$app['payment'] 	= $payment;
// 		// dd($app);

// $app['client_id'] = 'f3d259ddd3ed8ff3843839b';
$app[0]['client_id'] = 'f3d259ddd3ed8ff3843839b';
$app[0]['shipment']['receipt_number'] = 'f3d259ddd3ed8ff3843839b0';
// // dd($app);
// 		$token = $jwt->createToken($payload);
// dd($token);
		// $try 						= new \App\Models\Sale;
		// $try->fill(['user_id' => 1]);
		// if(!$try->save())
		// {
		// 	return response()->json( JSend::fail($try->getError())->asArray());
		// 	dd($try->getError());
		// }
		// dd(9);

		//1. Validate Sale Parameter
		// $sale						= Input::get('sale');

		// $this->class 		= new \App\Services\VeritransProcessingPayment;

		// $this->class->fill($app);
		$this->class->fill($app[0]);
		// $this->class->fill($modified);

		if(!$this->class->save())
		{
			return new JSend('error', (array)Input::all(), $this->class->getError());
		}

		return response()->json( JSend::success(['data' => $this->class->getData()->toArray()])->asArray())
					->setCallback($this->request->input('callback'));

		return new JSend('success', (array)$this->class->getData()->toArray());
	}
}
