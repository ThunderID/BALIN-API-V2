<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Carbon\Carbon;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use GenTux\Jwt\JwtToken;
use GenTux\Jwt\GetsJwtToken;

use App\Services\BalinRegisterCustomerByInvitation;
use App\Services\BalinRegisterCustomer;
use App\Services\BalinAccountActivate;
use App\Services\BalinResetPassword;
use App\Services\BalinResettingPassword;
use App\Services\BalinChangePassword;

class AuthController extends Controller
{
	use GetsJwtToken;

	public $token;

	public function __construct(Request $request, JwtToken $jwt, BalinRegisterCustomerByInvitation $regist_by_invite, BalinRegisterCustomer $register, BalinAccountActivate $activate, BalinResetPassword $reset, BalinResettingPassword $resetting, BalinChangePassword $changepwd)
	{
		$this->token 					= $jwt;
		$this->request 					= $request;
		$this->regist_by_invite 		= $regist_by_invite;
		$this->register 				= $register;
		$this->activate 				= $activate;
		$this->reset 					= $reset;
		$this->resetting 				= $resetting;
		$this->changepwd 				= $changepwd;
	}

	/**
	 * Authenticate user
	 *
	 * @return Response
	 */
	public function ssosignin($sso_data)
	{
		//1. check sso
		$sso 						= \App\Entities\Customer::email($sso_data['email'])->ssomedia(['facebook'])->first();

		//1a. register sso
		if(!$sso)
		{
			$customer				= 	[
											'id'			=> '',
											'name'			=> $sso_data['name'],
											'email'			=> $sso_data['email'],
											'gender'		=> $sso_data['user']['gender'],
											'sso_id'		=> $sso_data['id'],
											'sso_media'		=> 'facebook',
											'sso_data'		=> json_encode($sso_data['user']),
											'role'			=> 'customer',
										];

			if(isset($sso_data['reference_code']))
			{
				$customer['reference_code']		= $sso_data['reference_code'];
				$customer['invitation_link']	= $sso_data['invitation_link'];

				$customer_store 				= $this->regist_by_invite;
			}
			else
			{
				$customer_store 				= $this->register;
			}

			$customer_store->fill($customer);

			if(!$customer_store->save())
			{
				return response()->json( JSend::error($customer_store->getError()->toArray())->asArray());
			}

			$user					= $customer_store->getData();
		}
		else
		{
			$user 					= $sso;
		}

		$token 						= $this->token->createToken($user);
		$issue['token']['token']	= $token;
		$issue['me']				= $user->toArray();
		
		return response()->json( JSend::success($issue)->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Register customer
	 *
	 * @return Response
	 */
	public function signup()
	{
		if(!Input::has('customer'))
		{
			return response()->json( JSend::error(['Tidak ada data customer.'])->asArray());
		}

		$customer                   = Input::get('customer');

		if(isset($customer['reference_code']))
		{
			$customer_store 		= $this->regist_by_invite;
		}
		else
		{
			$customer_store 		= $this->register;
		}
		
		$customer_store->fill($customer);

		if(!$customer_store->save())
		{
			return response()->json( JSend::error($customer_store->getError()->toArray())->asArray());
		}


		return response()->json( JSend::success($customer_store->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Activate user account
	 *
	 * @return Response
	 */
	public function activate()
	{
		if(!Input::has('link'))
		{
			return response()->json( JSend::error(['Tidak ada data customer.'])->asArray());
		}

		$link					= Input::get('link');

		//1. Check Link
		$customer				= ['activation_link' => $link];

		$customer_store 		= $this->activate;
		
		$customer_store->fill($customer);

		if(!$customer_store->save())
		{
			return response()->json( JSend::error($customer_store->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($customer_store->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Get forgot link
	 *
	 * @return Response
	 */
	public function forgot()
	{
		if(!Input::has('email'))
		{
			return response()->json( JSend::error(['Tidak ada data email.'])->asArray());
		}

		$email					= Input::get('email');

		//1. Check Link
		$customer				= ['email' => $email];

		$customer_store 		= $this->reset;
		
		$customer_store->fill($customer);

		if(!$customer_store->save())
		{
			return response()->json( JSend::error($customer_store->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($customer_store->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Validate Reset link
	 *
	 * @return Response
	 */
	public function reset($link = '')
	{
		//1. Check Link
		$customer				= ['reset_password_link' => $link];

		$customer_store 		= $this->resetting;
		
		$customer_store->fill($customer);

		if(!$customer_store->save())
		{
			return response()->json( JSend::error($customer_store->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($customer_store->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Change password
	 *
	 * @return Response
	 */
	public function change()
	{
		if(!Input::has('email') || !Input::has('password'))
		{
			return response()->json( JSend::error(['Tidak ada data email dan password.'])->asArray());
		}

		$email					= Input::get('email');
		$password				= Input::get('password');

		//1. Check Link
		$customer				= ['email' => $email, 'password' => $password];

		$customer_store 		= $this->changepwd;
		
		$customer_store->fill($customer);

		if(!$customer_store->save())
		{
			return response()->json( JSend::error($customer_store->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($customer_store->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}

	public function createToken(JwtToken $jwt)
    {
    	\Log::info('Require Access Token Start');

    	$time_start = microtime(true); 

		if(Input::has('email'))
		{
			$check						= Auth::attempt(['email' => Input::get('email'), 'password' => Input::get('password')]);
			$user						= Auth::user();

			if(!$check)
			{
				if(Input::has('sso'))
				{
					return $this->ssosignin(Input::get('sso'));
				}
				else
				{
					return response()->json( JSend::error(['Email / Password Tidak Valid'])->asArray());
				}
			}
		}
		else
		{
			$user 						= new \App\Entities\User;
		}

        $token 							= $jwt->createToken($user);
		$issue['token']['token']		= $token;
		$issue['me']					= $user->toArray();
		
    	$time_end = microtime(true); 
		$execution_time = ($time_end - $time_start)/60;

		\Log::info('Require Access Token End '.$execution_time);
    	
    	return response()->json( JSend::success($issue)->asArray())
					->setCallback($this->request->input('callback'));
    }


	public function getMe()
    {
        $payload                    = $this->jwtPayload();
		
		if($payload['context']['role']=='customer')
		{
			$user						= \App\Entities\Customer::id($payload['context']['id'])->first()->toArray();
		}
		else
		{
			$user						= \App\Entities\Admin::id($payload['context']['id'])->first()->toArray();
		}

		return response()->json( JSend::success($user)->asArray())
					->setCallback($this->request->input('callback'));
    }
}
