<?php

namespace App\Services\Policies;

use App\Entities\Customer;

use App\Contracts\Policies\ValidatingRegisterUserInterface;

use Illuminate\Support\MessageBag;

class ValidatingRegisterUser implements ValidatingRegisterUserInterface
{
	public $errors;
	
	public $customer;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 	= new MessageBag;
	}

	public function validatecustomer(array $customer)
	{
		if(!isset($customer['email']))
		{
			$this->errors->add('Customer', 'Email tidak boleh kosong');
		}

		$exists_mail 			= Customer::email($customer['email'])->notid($customer['id'])->first();

		if($exists_mail)
		{
			$this->errors->add('Customer', 'Email sudah pernah terdaftar');
		}
	}

	public function validateactivationlink(array $customer)
	{
		$exists_customer 				= Customer::activationlink($customer['activation_link'])->active(false)->first();

		if(!$exists_customer)
		{
			$this->errors->add('Customer', 'Link tidak valid');
		}

		$this->customer 				= $exists_customer;
	}
	
	public function validateresetpassword(array $customer)
	{
		$exists_customer 				= Customer::email($customer['email'])->notSSOMedia(['facebook'])->first();

		if(!$exists_customer)
		{
			$this->errors->add('Customer', 'Email tidak valid');
		}

		$this->customer 				= $exists_customer;
	}
	
	public function validateresetpasswordlink(array $customer)
	{
		$exists_customer 				= Customer::resetpasswordlink($customer['reset_password_link'])->notSSOMedia(['facebook'])->first();

		if(!$exists_customer)
		{
			$this->errors->add('Customer', 'Link tidak valid');
		}

		$this->customer 				= $exists_customer;
	}

	public function validatechangepassword(array $customer)
	{
		$exists_customer 				= Customer::email($customer['email'])->notSSOMedia(['facebook'])->first();

		if(!$exists_customer)
		{
			$this->errors->add('Customer', 'Tidak bisa mengubah password untuk email yang login dengan akun facebook');
		}

		if($exists_customer && $exists_customer->reset_password_link!='')
		{
			$this->errors->add('Customer', 'Password sedang dalam kondisi reset');
		}

		$this->customer 				= $exists_customer;
	}
	

	public function getreferralcode(array $customer)
	{
		$letters 							= 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		
		do
		{
			$names							= explode(' ', $customer['name']);
			$fnames 						= [];
			$lnames 						= [];
			$lostcode 						= [];
			if(isset($names[0]))
			{
				$fname 						= str_split($names[0]);

				foreach ($fname as $key => $value) 
				{
					if($key <= 2)
					{
						$fnames[$key]		= $value;
					}
				}
			}

			if(count($fnames) < 3)
			{
				foreach (range((count($fnames)-1), 2) as $key) 
				{
					$fnames[$key] 			= substr(str_shuffle($letters), 0, 1);
				}
			}

			if(isset($names[count($names)-1]))
			{
				$lname 						= str_split($names[count($names)-1]);
				foreach ($lname as $key => $value) 
				{
					if($key <= 2)
					{
						$lnames[$key]		= $value;
					}
				}
			}

			if(count($lnames) < 3)
			{
				foreach (range((count($lnames)-1), 2) as $key) 
				{
					$lnames[$key] 			= substr(str_shuffle($letters), 0, 1);
				}
			}

			foreach (range(0, 1) as $key) 
			{
				$lostcode[$key] 			= substr(str_shuffle($letters), 0, 1);
			}

			$lcode 							= implode('', $lnames);
			$fcode 							= implode('', $fnames);
			$locode 						= implode('', $lostcode);

			$referral_code 		            = strtolower($fcode.$lcode.$locode);

			$referral                       = Customer::referralcode($fcode.$lcode.$locode)->first();
		}
		while($referral);

		$customer['referral_code']			= $referral_code;

		$this->customer 					= $customer;
	}


	public function getactivationlink(array $customer)
	{
		do
		{
			$activation_link				= md5(uniqid(rand(), TRUE));
	
			$exists_link					= Customer::activationlink($activation_link)->first();
		}
		while($exists_link);

		$customer['activation_link']		= $activation_link;

		$this->customer 					= $customer;
	}

	public function getresetpassword(Customer $customer)
	{
		do
		{
			$reset_password_link			= md5(uniqid(rand(), TRUE));
	
			$exists_link					= Customer::resetpasswordlink($reset_password_link)->first();
		}
		while($exists_link);

		return $reset_password_link;
	}
}

