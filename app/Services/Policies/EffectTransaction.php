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

	public function sendmailinvoice(Sale $sale)
	{
		$data				= ['invoice' => $sale, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.balin.order.invoice', ['data' => $data], function($message) use($sale)
		{
			$message->to($sale['user']['email'], $sale['user']['name'])->subject(strtoupper('BALIN').' - INVOICE');
		}); 
	}

	public function sendmailpaymentacceptance(Sale $sale)
	{
		$data				= ['paid' => $sale, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.balin.order.paid', ['data' => $data], function($message) use($sale)
		{
			$message->to($sale['user']['email'], $sale['user']['name'])->subject(strtoupper('BALIN').' - PAYMENT VALIDATION');
		}); 
	}

	public function sendmailcancelorder(Sale $sale)
	{
		$data				= ['canceled' => $sale, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.balin.order.canceled', ['data' => $data], function($message) use($sale)
		{
			$message->to($sale['user']['email'], $sale['user']['name'])->subject(strtoupper('BALIN').' - CANCEL ORDER');
		}); 
	}

	public function sendmaildeliveredorder(Sale $sale)
	{
		$data				= ['delivered' => $sale, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.balin.order.delivered', ['data' => $data], function($message) use($sale)
		{
			$message->to($sale['user']['email'], $sale['user']['name'])->subject(strtoupper('BALIN').' - DELIVERED ORDER');
		}); 
	}
}
