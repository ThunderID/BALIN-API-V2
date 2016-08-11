<?php

namespace App\Services\Policies;

use App\Entities\Customer;
use App\Entities\Store;
use App\Entities\Policy;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

use App\Contracts\Policies\EffectRegisterUserInterface;

class EffectRegisterUser implements EffectRegisterUserInterface
{
	public $errors;

	protected $storeinfo;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 		= new MessageBag;
		$storeinfo			= new Store;
		$storeinfo 			= $storeinfo->default(true)->get()->toArray();
		$policy				= new Policy;
		$policy 			= $policy->default(true)->get()->toArray();

		foreach ($storeinfo as $key => $value) 
		{
			$this->storeinfo[strtolower($value['type'])]	 = $value['value'];
		}

		foreach ($policy as $key => $value) 
		{
			$this->storeinfo[strtolower($value['type'])]	 = $value['value'];
		}
	}

	public function sendactivationmail(Customer $customer)
	{
		$template 					= 'balin';

		$this->storeinfo['action']	= env('BALIN_ACTION_BASEURL', 'https://balin.id').'/activation/link/'.$customer['activation_link'];

		$data						= ['user' => $customer, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.balin.crm.welcome', ['data' => $data], function (Message $message) use ($customer, $data) 
		{
			$message->to($customer['email'], $customer['name'])
			->subject(strtoupper('BALIN').' - WELCOME MAIL')
			->from('cs@balin.id', 'BALIN INDONESIA')
			->embedData(['data' => $data], 'sendgrid/x-smtpapi');
		});
	}
	
	public function sendresetpasswordmail(Customer $customer)
	{
		$this->storeinfo['action']	= env('BALIN_ACTION_BASEURL', 'https://balin.id').'/reset/password/'.$customer['reset_password_link'];

		$data						= ['user' => $customer, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.balin.account.password', ['data' => $data], function (Message $message) use ($customer, $data) 
		{
			$message->to($customer['email'], $customer['name'])
			->subject(strtoupper('BALIN').' - RESET PASSWORD')
			->from('cs@balin.id', 'BALIN INDONESIA')
			->embedData(['data' => $data], 'sendgrid/x-smtpapi');
		});
	}

	public function contactusmail(array $customer)
	{
		$data						= ['customer' => $customer, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.balin.crm.contact', ['data' => $data], function (Message $message) use ($customer, $data) 
		{
			$message->to($this->storeinfo['email'], 'BALIN - CS')
			->subject(strtoupper('BALIN').' - RESET PASSWORD')
			->from('cs@balin.id', 'CUSTOMER FEEDBACK')
			->embedData(['data' => $data], 'sendgrid/x-smtpapi');
		});
	}
}
