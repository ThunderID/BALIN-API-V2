<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

use App\Libraries\JSend;

use App\Entities\Sale;
use App\Entities\Customer;
use App\Contracts\Policies\EffectRegisterUserInterface;
use App\Contracts\Policies\EffectTransactionInterface;

use \Exception;

/**
 * Handle order mail sender
 * 
 * @author cmooy
 */
class CRMController extends Controller
{
	protected $request;
	protected $post;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(Request $request, EffectRegisterUserInterface $post, EffectTransactionInterface $post_sale)
	{
		$this->request 		= $request;
		$this->post 		= $post;
		$this->post_sale 	= $post_sale;
	}

	/**
	 * Send balin welcome
	 *
	 * @param user, store
	 * @return JSend Response
	 */
	public function welcome()
	{
		$user 					= Input::get('user');

		$this->post->sendactivationmail(Customer::id($user['id'])->first());

		return response()->json( JSend::success(['Email terkirim'])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Send balin abandoned
	 *
	 * @param user, store
	 * @return JSend Response
	 */
	public function abandoned()
	{
		$cart 					= Input::get('cart');

		$this->post_sale->sendmailabandonedcart(Sale::id($cart['id'])->status(['cart'])->first());

		return response()->json( JSend::success(['Email terkirim'])->asArray())
					->setCallback($this->request->input('callback'));
	}


	/**
	 * Send balin contact
	 *
	 * @param user, store
	 * @return JSend Response
	 */
	public function contact()
	{
		$customer 				= Input::get('customer');

		$this->post->contactusmail($customer);

		return response()->json( JSend::success(['Email terkirim'])->asArray())
					->setCallback($this->request->input('callback'));
	}
}
