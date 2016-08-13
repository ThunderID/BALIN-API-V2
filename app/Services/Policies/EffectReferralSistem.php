<?php

namespace App\Services\Policies;

use App\Entities\Store;
use App\Entities\Policy;
use App\Entities\Customer;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

use App\Contracts\Policies\EffectReferralSistemInterface;

class EffectReferralSistem implements EffectReferralSistemInterface
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

	public function sendmailpointreminder(array $point, array $product)
	{
		$this->storeinfo['action'] 	= env('BALIN_ACTION_BASEURL', 'https://balin.id').'/products';
		$data						= ['point' => $point, 'balin' => $this->storeinfo, 'product' => $product];

		//send mail
		Mail::send('mail.balin..crm.point', ['data' => $data], function (Message $message) use ($point, $data) 
		{
			$message->to($point['user']['email'], $point['user']['name'])
			->subject(strtoupper('BALIN').' - POINT REMINDER')
			->from('cs@balin.id', 'BALIN INDONESIA')
			->embedData(['data' => $data], 'sendgrid/x-smtpapi');
		});
	}
	
	public function sendinvitationmail(Customer $customer, $email, $link)
	{
		$this->storeinfo['action'] 	= env('BALIN_ACTION_BASEURL', 'https://balin.id').'/invite/by/'.$customer['code_referral'].'/at/'.$link;
		
		$product                     = \App\Entities\Product::sellable(true)->stats(0)->take(4)->get()->toArray();

		$data						= ['user' => $customer, 'balin' => $this->storeinfo, 'product'=> $product];

		//send mail
		Mail::send('mail.balin.account.invitation', ['data' => $data], function (Message $message) use ($customer, $data, $email) 
		{
			$message->to($email, $email)
			->subject(strtoupper('BALIN').' - INVITATION FROM '.strtoupper($customer['name']))
			->from('cs@balin.id', 'BALIN INDONESIA')
			->embedData(['data' => $data], 'sendgrid/x-smtpapi');
		});
	}
}
