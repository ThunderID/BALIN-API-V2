<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Artisan;

/**
 * Handle Protected Resource of Sale
 * 
 * @author cmooy
 */
class PaymentController extends Controller
{
	public function __construct(Request $request)
	{
		$this->request 				= $request;
	}
	
	/**
	 * Veritrans Credit Card
	 *
	 * 1. Check Order
	 * 2. Save Payment
	 * 
	 * @return Response
	 */
	public function veritranscc()
	{
		Artisan::call('veritrans:notification');

		return response()->json( JSend::success(Input::all())->asArray())
					->setCallback($this->request->input('callback'));
	}
}
