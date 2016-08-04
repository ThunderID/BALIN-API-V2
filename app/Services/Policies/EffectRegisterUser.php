<?php

namespace App\Services\Policies;

use App\Entities\Customer;
use App\Entities\Store;
use App\Entities\Policy;
use App\Models\ClientTemplate;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Mail;

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
		$storeinfo			= Store::default(true)->get()->toArray();
		$policy				= Policy::default(true)->get()->toArray();

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
		$template 			= 'balin';

		$data				= ['user' => $customer, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.balin.crm.welcome', ['data' => $data], function($message) use($customer)
		{
			$message->to($customer['email'], $customer['name'])->subject(strtoupper('BALIN').' - WELCOME MAIL');
		}); 
	}
}
