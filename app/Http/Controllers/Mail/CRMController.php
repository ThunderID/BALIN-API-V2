<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

use App\Libraries\JSend;

use App\Entities\Customer;
use App\Contracts\Policies\EffectRegisterUserInterface;

use \Exception;

/**
 * Handle order mail sender
 * 
 * @author cmooy
 */
class CRMController extends Controller
{
	protected $customer;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $post;
	protected $pro;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(Request $request, EffectRegisterUserInterface $post)
	{
		$this->request 	= $request;
		$this->post 	= $post;
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

		return response()->json( JSend::success(['Email terkirim']))
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
		$store 					= Input::get('store');

		// checking cart data
		if(empty($cart))
		{
			throw new Exception('Sent variable must be array of a record.');
		}

		// checking store data
		if(empty($store))
		{
			throw new Exception('Sent variable must be array of a record.');
		}

		$data						= ['cart' => $cart, 'balin' => $store];

		//send mail
		Mail::send('mail.'.$this->template.'.crm.abandoned', ['data' => $data], function($message) use($cart)
		{
			$message->to($cart['user']['email'], $cart['user']['name'])->subject(strtoupper($this->template).' - FRIENDLY REMINDER');
		}); 
		
		return new JSend('success', (array)Input::all());
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
		$store 					= Input::get('store');

		// checking customer data
		if(empty($customer))
		{
			throw new Exception('Sent variable must be array of a record.');
		}

		// checking store data
		if(empty($store))
		{
			throw new Exception('Sent variable must be array of a record.');
		}

		$data						= ['customer' => $customer, 'balin' => $store];

		//send mail
		Mail::send('mail.'.$this->template.'.crm.contact', ['data' => $data], function($message) use($customer)
		{
			$message->to($store['email'], strtoupper($this->template).' CS ')->subject(strtoupper($this->template).' - CUSTOMER FEEDBACK');
		}); 
		
		return new JSend('success', (array)Input::all());
	}
}
