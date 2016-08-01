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

	public function sendactivationmail(Customer $customer, $client_id)
	{
		$template 			= ClientTemplate::clientid($client_id)->first();

		$data				= ['user' => $customer, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.'.$template->located.'.crm.welcome', ['data' => $data], function($message) use($customer, $template)
		{
			$message->to($customer['email'], $customer['name'])->subject(strtoupper($template->located).' - WELCOME MAIL');
		}); 
	}
}
