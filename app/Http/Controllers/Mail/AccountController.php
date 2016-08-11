<?php

namespace App\Http\Controllers\Mail;

use App\Libraries\JSend;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

use App\Contracts\Policies\EffectRegisterUserInterface;
use App\Contracts\Policies\EffectReferralSistemInterface;

use \Exception;
use App\Entities\Customer;

/**
 * Handle order mail sender
 * 
 * @author cmooy
 */
class AccountController extends Controller
{
	protected $request;
	protected $post;
	protected $post_ref;

	function __construct(Request $request, EffectRegisterUserInterface $post, EffectReferralSistemInterface $post)
	{
		$this->request 		= $request;
		$this->post 		= $post;
		$this->post_ref 	= $post_ref;
	}

	/**
	 * Send balin reset password
	 *
	 * @param user, store
	 * @return JSend Response
	 */
	public function password()
	{
		$user 				= Input::get('user');
		
		$this->post->sendresetpasswordmail(Customer::id($user['id'])->first());

		return response()->json( JSend::success(['Email terkirim'])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Send balin invitation
	 *
	 * @param user, store
	 * @return JSend Response
	 */
	public function invitation()
	{
		$user 				= Input::get('user');
		
		$this->post_ref->sendinvitationmail(Customer::id($user['id'])->first());

		return response()->json( JSend::success(['Email terkirim'])->asArray())
					->setCallback($this->request->input('callback'));
	}
}
