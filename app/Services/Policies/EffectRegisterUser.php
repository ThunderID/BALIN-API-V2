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

		$this->storeinfo['action']	= 'https://balin.id/activation/link/'.$customer['activation_link'];

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
}
