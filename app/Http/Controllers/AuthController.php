<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use \GenTux\Jwt\JwtToken;
use \GenTux\Jwt\GetsJwtToken;

class AuthController extends Controller
{
	use GetsJwtToken;

	public $token;

	function __construct(JwtToken $jwt)
	{
		$this->token 					= $jwt;
	}

	/**
	 * Authenticate user
	 *
	 * @return Response
	 */
	public function signin()
	{
		$email                          = Input::get('email');
		$password                       = Input::get('password');
		
		$check                          = Auth::attempt(['email' => $email, 'password' => $password]);

		if ($check)
		{
			$result['id']               = Auth::user()['id'];
			$result['name']             = Auth::user()['name'];
			$result['email']            = Auth::user()['email'];
			$result['date_of_birth']    = Auth::user()['date_of_birth'];
			$result['role']             = Auth::user()['role'];
			$result['gender']           = Auth::user()['gender'];

			return new JSend('success', (array)$result);
		}
		elseif(Input::has('sso'))
		{
			$sso_data 					= Input::get('sso');
			//1. check sso
			$sso 						= \App\Entities\User::email($sso_data['email'])->ssomedia(['facebook'])->first();

			//1a. register sso
			if(!$sso)
			{
				$sso					= new \App\Entities\Customer;

				$sso->fill([
						'name'			=> $sso_data['name'],
						'email'			=> $sso_data['email'],
						'gender'		=> $sso_data['user']['gender'],
						'sso_id'		=> $sso_data['id'],
						'sso_media'		=> 'facebook',
						'sso_data'		=> json_encode($sso_data['user']),
						'role'			=> 'customer',
					]);

				if (!$sso->save())
				{
					return new JSend('error', (array)Input::all(), $sso->getError());
				}

				$is_new					= true;
			}
			
			Auth::loginUsingId($sso['id']);

			$result['id']               = Auth::user()['id'];
			$result['name']             = Auth::user()['name'];
			$result['email']            = Auth::user()['email'];
			$result['date_of_birth']    = Auth::user()['date_of_birth'];
			$result['role']             = Auth::user()['role'];
			$result['gender']           = Auth::user()['gender'];

			return new JSend('success', (array)$result);
		}
		
		return new JSend('error', (array)Input::all(), 'Username atau password tidak valid.');
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

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Validate User Parameter
		if(is_null($customer['id']))
		{
			$is_new                 = true;
		}
		else
		{
			$is_new                 = false;
		}

		$customer_rules             =   [
											'name'                          => 'required|max:255',
											'email'                         => 'required|max:255|unique:users,email,'.(!is_null($customer['id']) ? $customer['id'] : ''),
											'password'                      => 'max:255',
											'sso_id'                        => '',
											'sso_media'                     => 'in:facebook',
											'sso_data'                      => '',
											'gender'                        => 'in:male,female',
											'role'							=> 'required|in:customer',
											'date_of_birth'                 => 'date_format:"Y-m-d H:i:s"',
										];

		//1a. Get original data
		$customer_data              = \App\Entities\Customer::findornew($customer['id']);

		//1b. Validate Basic Customer Parameter
		$validator                  = Validator::make($customer, $customer_rules);

		if (!$validator->passes())
		{
			$errors->add('Customer', $validator->errors());
		}
		else
		{
			//if validator passed, save customer
			$customer_data           = $customer_data->fill($customer);

			if(!$customer_data->save())
			{
				$errors->add('Customer', $customer_data->getError());
			}
		}


		//2. check invitation
		if(!$errors->count() && isset($customer['reference_code']))
		{
			$referral_data					= \App\Entities\Referral::code($customer['reference_code'])->first();

			if(!$referral_data)
			{
				$errors->add('Redeem', 'Link tidak valid. Silahkan mendaftar dengan menu biasa.');
			}
			elseif($referral_data->quota <= 0)
			{
				$errors->add('Redeem', 'Quota referral sudah habis.');
			}
			else
			{
				$store                      = \App\Entities\StoreSetting::type('voucher_point_expired')->Ondate('now')->first();

				if($store)
				{
					$expired_at             = new Carbon($store->value);
				}
				else
				{
					$expired_at             = new Carbon('+ 3 months');
				}

				//if validator passed, save referral
				$point                  =   [
												'user_id'               => $customer_data['id'],
												'reference_id'        	=> $referral_data['user_id'],
												'reference_type'        => 'App\Entities\User',
												'expired_at'            => $expired_at->format('Y-m-d H:i:s'),
											];

				$point_data             = new \App\Entities\PointLog;
				
				$point_data->fill($point);

				if(!$point_data->save())
				{
					$errors->add('Redeem', $point_data->getError());
				}
			}
		}

		if(!$errors->count() && isset($referral_data))
		{
			$invitation 				= \App\Entities\UserInvitationLog::email($customer_data['email'])->userid($referral_data['user_id'])->first();

			if($invitation)
			{
				$invitation->is_used 	= true;

				if(!$invitation->save())
				{
					$errors->add('Invitation', $invitation->getError());
				}
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
			$user						= \App\Entities\User::email(\Illuminate\Support\Facades\Input::get('email'))->first();
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
			$user						= \App\Entities\Customer::id($payload['context']['id'])->first();
		}
		else
		{
			$user						= \App\Entities\Admin::id($payload['context']['id'])->first();
		}

		return new \App\Libraries\JSend('success', (array)$user);
    }
}
