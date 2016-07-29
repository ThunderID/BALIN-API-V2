<?php

namespace App\Services\Policies;

use App\Entities\Sale;
use App\Entities\Store;
use App\Entities\Policy;
use App\Models\ClientTemplate;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Mail;

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

	public function sendmailinvoice(Sale $sale, $client_id)
	{
		$template 			= ClientTemplate::clientid($client_id)->first();

		$data				= ['invoice' => $sale, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.'.$template->located.'.order.invoice', ['data' => $data], function($message) use($sale, $template)
		{
			$message->to($sale['user']['email'], $sale['user']['name'])->subject(strtoupper($template->located).' - INVOICE');
		}); 
	}

	public function sendmailpaymentacceptance(Sale $sale, $client_id)
	{
		$template 			= ClientTemplate::clientid($client_id)->first();

		$data				= ['paid' => $sale, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.'.$template->located.'.order.paid', ['data' => $data], function($message) use($sale, $template)
		{
			$message->to($sale['user']['email'], $sale['user']['name'])->subject(strtoupper($template->located).' - PAYMENT VALIDATION');
		}); 
	}

	public function sendmailcancelorder(Sale $sale, $client_id)
	{
		$template 			= ClientTemplate::clientid($client_id)->first();

		$data				= ['canceled' => $sale, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.'.$template->located.'.order.canceled', ['data' => $data], function($message) use($sale, $template)
		{
			$message->to($sale['user']['email'], $sale['user']['name'])->subject(strtoupper($template->located).' - CANCEL ORDER');
		}); 
	}

	public function sendmaildeliveredorder(Sale $sale, $client_id)
	{
		$template 			= ClientTemplate::clientid($client_id)->first();

		$data				= ['delivered' => $sale, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.'.$template->located.'.order.delivered', ['data' => $data], function($message) use($sale, $template)
		{
			$message->to($sale['user']['email'], $sale['user']['name'])->subject(strtoupper($template->located).' - DELIVERED ORDER');
		}); 
	}
}
