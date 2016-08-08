<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Controller;

use App\Entities\Sale;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

use App\Libraries\JSend;

use App\Contracts\Policies\EffectTransactionInterface;

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

	function __construct(Request $request, EffectTransactionInterface $post)
	{
		$this->request 	= $request;
		$this->post 	= $post;
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

		return response()->json( JSend::success(['Email terkirim']))
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

		return response()->json( JSend::success(['Email terkirim']))
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
		$shipped 				= Input::get('order');
		$store 					= Input::get('store');

		// checking shipped data
		if(empty($shipped))
		{
			throw new Exception('Sent variable must be array of a record.');
		}

		// checking store data
		if(empty($store))
		{
			throw new Exception('Sent variable must be array of a record.');
		}

		$data						= ['shipped' => $shipped, 'balin' => $store];

		//send mail
		Mail::send('mail.'.$this->template.'.order.shipped', ['data' => $data], function($message) use($shipped)
		{
			$message->to($shipped['user']['email'], $shipped['user']['name'])->subject(strtoupper($this->template).' - SHIPPING INFORMATION');
		}); 
		
		return new JSend('success', (array)Input::all());
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
		$store 					= Input::get('store');

		// checking delivered data
		if(empty($delivered))
		{
			throw new Exception('Sent variable must be array of a record.');
		}

		// checking store data
		if(empty($store))
		{
			throw new Exception('Sent variable must be array of a record.');
		}

		$data						= ['delivered' => $delivered, 'balin' => $store];

		//send mail
		Mail::send('mail.'.$this->template.'.order.delivered', ['data' => $data], function($message) use($delivered)
		{
			$message->to($delivered['user']['email'], $delivered['user']['name'])->subject(strtoupper($this->template).' - DELIVERED ORDER');
		}); 
		
		return new JSend('success', (array)Input::all());
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
		$store 					= Input::get('store');

		// checking canceled data
		if(empty($canceled))
		{
			throw new Exception('Sent variable must be array of a record.');
		}

		// checking store data
		if(empty($store))
		{
			throw new Exception('Sent variable must be array of a record.');
		}

		$data						= ['canceled' => $canceled, 'balin' => $store];

		//send mail
		Mail::send('mail.'.$this->template.'.order.canceled', ['data' => $data], function($message) use($canceled)
		{
			$message->to($canceled['user']['email'], $canceled['user']['name'])->subject(strtoupper($this->template).' - CANCEL ORDER');
		}); 
		
		return new JSend('success', (array)Input::all());
	}
}
