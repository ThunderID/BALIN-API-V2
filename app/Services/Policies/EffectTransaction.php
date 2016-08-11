<?php

namespace App\Services\Policies;

use App\Entities\Sale;
use App\Entities\Store;
use App\Entities\Policy;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

use App\Contracts\Policies\EffectTransactionInterface;

class EffectTransaction implements EffectTransactionInterface
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

	public function sendmailinvoice(Sale $sale)
	{
		$data				= ['invoice' => $sale, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.balin.order.invoice', ['data' => $data], function (Message $message) use ($sale, $data) 
		{
			$message->to($sale['user']['email'], $sale['user']['name'])
			->subject(strtoupper('BALIN').' - INVOICE')
			->from('cs@balin.id', 'BALIN INDONESIA')
			->embedData(['data' => $data], 'sendgrid/x-smtpapi');
		});
	}

	public function sendmailpaymentacceptance(Sale $sale)
	{
		$data				= ['paid' => $sale, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.balin.order.paid', ['data' => $data], function (Message $message) use ($sale, $data) 
		{
			$message->to($sale['user']['email'], $sale['user']['name'])
			->subject(strtoupper('BALIN').' - PAYMENT VALIDATION')
			->from('cs@balin.id', 'BALIN INDONESIA')
			->embedData(['data' => $data], 'sendgrid/x-smtpapi');
		});
	}

	public function sendmailcancelorder(Sale $sale)
	{
		$data				= ['canceled' => $sale, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.balin.order.canceled', ['data' => $data], function (Message $message) use ($sale, $data) 
		{
			$message->to($sale['user']['email'], $sale['user']['name'])
			->subject(strtoupper('BALIN').' - CANCEL ORDER')
			->from('cs@balin.id', 'BALIN INDONESIA')
			->embedData(['data' => $data], 'sendgrid/x-smtpapi');
		});
	}

	public function sendmaildeliveredorder(Sale $sale)
	{
		$data				= ['delivered' => $sale, 'balin' => $this->storeinfo];
		
		//send mail
		Mail::send('mail.balin.order.delivered', ['data' => $data], function (Message $message) use ($sale, $data) 
		{
			$message->to($sale['user']['email'], $sale['user']['name'])
			->subject(strtoupper('BALIN').' - DELIVERED ORDER')
			->from('cs@balin.id', 'BALIN INDONESIA')
			->embedData(['data' => $data], 'sendgrid/x-smtpapi');
		});
	}

	public function sendmailabandonedcart(Sale $sale)
	{
		$data				= ['cart' => $sale, 'balin' => $this->storeinfo];
		
		//send mail
		Mail::send('mail.balin.crm.abandoned', ['data' => $data], function (Message $message) use ($sale, $data) 
		{
			$message->to($sale['user']['email'], $sale['user']['name'])
			->subject(strtoupper('BALIN').' - FRIENDLY REMINDER')
			->from('cs@balin.id', 'BALIN INDONESIA')
			->embedData(['data' => $data], 'sendgrid/x-smtpapi');
		});
	}
}
