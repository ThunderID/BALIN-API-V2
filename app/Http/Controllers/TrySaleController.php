<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use App\Services\VeritransProcessingPayment as Checkout;
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
        
		// $user 	= \App\Models\User::first();
		// $payload 	= 	[
		// 					'sub' => $user->id,
		// 				    'exp' => time() + 7200,
		// 				    'context' => [
		// 				        'email' => $user->email
		// 				    ]
		// 	            ];
		// $jwt 		= new JwtToken;

		$app 				= \App\Models\Sale::status('wait')->with(['voucher', 'transactionlogs', 'user', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'paidpointlogs', 'payment', 'shipment', 'shipment.address', 'shipment.courier', 'transactionextensions', 'transactionextensions.productextension'])->skip(1)->take(1)->get()->toArray();
// dd(9);
// $app[0]['client_id'] = 'f3d259ddd3ed8ff3843839b';
// dd($app);
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

		$this->class->fill($app[0]);

		if(!$this->class->save())
		{
			return new JSend('error', (array)Input::all(), $this->class->getError());
		}

		return response()->json( JSend::success(['data' => $this->class->getData()->toArray()])->asArray())
					->setCallback($this->request->input('callback'));

		return new JSend('success', (array)$this->class->getData()->toArray());
	}
}
