<?php

namespace App\Services\Policies;

use App\Entities\Sale;
use App\Entities\Store;
use App\Entities\Policy;
use App\Models\ClientTemplate;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Mail;

use App\Contracts\Policies\EffectShipmentInterface;

class EffectShipment implements EffectShipmentInterface
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

	public function sendmailshippingpackage(Sale $sale, $client_id)
	{
		$template 			= ClientTemplate::clientid($client_id)->first();

		$data				= ['shipped' => $sale, 'balin' => $this->storeinfo];

		//send mail
		Mail::send('mail.'.$template->located.'.order.shipped', ['data' => $data], function($message) use($sale, $template)
		{
			$message->to($sale['user']['email'], $sale['user']['name'])->subject(strtoupper($template->located).' - SHIPPING INFORMATION');
		}); 
	}
}
