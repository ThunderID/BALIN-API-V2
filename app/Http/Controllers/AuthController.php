<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Carbon\Carbon;
use \GenTux\Jwt\JwtToken;
use \GenTux\Jwt\GetsJwtToken;
use App\Services\BalinRegisterCustomer;

class AuthController extends Controller
{
	use GetsJwtToken;

	public $token;

	public function __construct(Request $request, JwtToken $jwt, BalinRegisterCustomer $register)
	{
		$this->token 					= $jwt;
		$this->register 				= $register;
		$this->request 					= $request;
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

			$customer_store 		= $this->register;
			
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
		
		return new \App\Libraries\JSend('success', (array)$issue);
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
			return new JSend('error', (array)Input::all(), 'Tidak ada data customer.');
		}

		$customer                   = Input::get('customer');
	
		$customer_store 			= $this->register;
		
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
			return new JSend('error', (array)Input::all(), 'Tidak ada data customer.');
		}

		$link                       = Input::get('link');

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Check Link
		$customer_data              = \App\Entities\Customer::activationlink($link)->first();

		if(!$customer_data)
		{
			$errors->add('Customer', 'Link tidak valid.');
		}
		elseif($customer_data->is_active)
		{
			$errors->add('Customer', 'Link tidak valid.');
		}
		else
		{
			//if validator passed, save customer
			$customer_data           = $customer_data->fill(['is_active' => true, 'activation_link' => '', 'date_of_birth' => ((strtotime($customer_data['date_of_birth'])) ? $customer_data['date_of_birth']->format('Y-m-d H:i:s') : '')]);

			if(!$customer_data->save())
			{
				$errors->add('Customer', $customer_data->getError());
			}
		}

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_customer                 = \App\Entities\Customer::id($customer_data['id'])->first()->toArray();

		return new JSend('success', (array)$final_customer);
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
			return new JSend('error', (array)Input::all(), 'Tidak ada data customer.');
		}

		$email						= Input::get('email');

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Check Link
		$customer_data              = \App\Entities\Customer::email($email)->notSSOMedia(['facebook'])->first();

		if(!$customer_data)
		{
			$errors->add('Customer', 'Email tidak valid.');
		}
		else
		{
			//if validator passed, save customer
			$customer_data           = $customer_data->fill(['reset_password_link' => $customer_data->generateResetPasswordLink(), 'date_of_birth' => (strtotime($customer_data['date_of_birth']) ? $customer_data['date_of_birth']->format('Y-m-d H:i:s') : '')]);

			if(!$customer_data->save())
			{
				$errors->add('Customer', $customer_data->getError());
			}
		}

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_customer                 = \App\Entities\Customer::id($customer_data['id'])->first()->toArray();

		return new JSend('success', (array)$final_customer);
	}

	/**
	 * Validate Reset link
	 *
	 * @return Response
	 */
	public function reset($link = '')
	{
		$errors                     = new MessageBag();

		//1. Check Link
		$customer_data              = \App\Entities\Customer::resetpasswordlink($link)->notSSOMedia(['facebook'])->first();

		if(!$customer_data)
		{
			$errors->add('Customer', 'Link tidak valid.');
		}

		if($errors->count())
		{
			return new JSend('error', (array)Input::all(), $errors);
		}

		return new JSend('success', (array)$customer_data->toArray());
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
			return new JSend('error', (array)Input::all(), 'Tidak ada data customer.');
		}

		$email						= Input::get('email');
		$password					= Input::get('password');

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Check Email
		$customer_data              = \App\Entities\Customer::email($email)->notSSOMedia(['facebook'])->first();

		if(!$customer_data)
		{
			$errors->add('Customer', 'Email tidak valid.');
		}
		elseif(empty($customer_data->reset_password_link))
		{
			$errors->add('Customer', 'Email tidak valid.');
		}
		else
		{
			//if validator passed, save customer
			$customer_data           = $customer_data->fill(['reset_password_link' => '', 'password' => $password, 'date_of_birth' => (strtotime($customer_data['date_of_birth']) ? $customer_data['date_of_birth']->format('Y-m-d H:i:s') : '')]);

			if(!$customer_data->save())
			{
				$errors->add('Customer', $customer_data->getError());
			}
		}

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_customer                 = \App\Entities\Customer::id($customer_data['id'])->first()->toArray();

		return new JSend('success', (array)$final_customer);
	}

	public function createToken(JwtToken $jwt)
    {
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
					return new JSend('error', (array)Input::all(), ['User tidak ada']);
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
		
		return new \App\Libraries\JSend('success', (array)$issue);
    }


	public function getme()
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

		return new \App\Libraries\JSend('success', (array)$user);
    }
}
