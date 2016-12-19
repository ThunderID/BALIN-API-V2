<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Controller;

use App\Entities\Sale;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

use App\Libraries\JSend;

use App\Contracts\Policies\EffectTransactionInterface;
use App\Contracts\Policies\EffectShipmentInterface;

use \Exception;

/**
 * Handle order mail sender
 * 
 * @author cmooy
 */
class OrderController extends Controller
{
	protected $request;
	protected $post;

	function __construct(Request $request, EffectTransactionInterface $post, EffectShipmentInterface $post_ship)
	{
		$this->request 		= $request;
		$this->post 		= $post;
		$this->post_ship 	= $post_ship;
	}

	/**
	 * Send balin invoice
	 *
	 * @param invoice, store
	 * @return JSend Response
	 */
	public function invoice()
	{
		$invoice 		= Input::get('invoice');

		$this->post->sendmailinvoice(Sale::id($invoice['id'])->status(['wait', 'veritrans_processing_payment'])->first());

		return response()->json( JSend::success(['Email terkirim'])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Send balin paid
	 *
	 * @param order, store
	 * @return JSend Response
	 */
	public function paid()
	{
		$paid 			= Input::get('order');

		$this->post->sendmailpaymentacceptance(Sale::id($paid['id'])->status(['paid'])->first());

		return response()->json( JSend::success(['Email terkirim'])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Send balin shipped
	 *
	 * @param order, store
	 * @return JSend Response
	 */
	public function shipped()
	{
		$shipped 			= Input::get('order');

		$this->post_ship->sendmailshippingpackage(Sale::id($shipped['id'])->status(['shipping'])->first());

		return response()->json( JSend::success(['Email terkirim'])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Send balin delivered
	 *
	 * @param order, store
	 * @return JSend Response
	 */
	public function delivered()
	{
		$delivered 				= Input::get('order');
		
		$this->post->sendmaildeliveredorder(Sale::id($delivered['id'])->status(['delivered'])->first());

		return response()->json( JSend::success(['Email terkirim'])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Send balin canceled
	 *
	 * @param order, store
	 * @return JSend Response
	 */
	public function canceled()
	{
		$canceled 				= Input::get('order');
		
		$this->post->sendmailcancelorder(Sale::id($canceled['id'])->status(['canceled'])->first());

		return response()->json( JSend::success(['Email terkirim'])->asArray())
					->setCallback($this->request->input('callback'));
	}
}
