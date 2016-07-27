<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\Queue;

use DB, Carbon\Carbon;

use \Illuminate\Support\MessageBag as MessageBag;

use App\Models\Sale;
use App\Http\Controllers\Veritrans\Veritrans_Config;
use App\Http\Controllers\Veritrans\Veritrans_Transaction;
use App\Http\Controllers\Veritrans\Veritrans_ApiRequestor;
use App\Http\Controllers\Veritrans\Veritrans_Notification;
use App\Http\Controllers\Veritrans\Veritrans_VtDirect;
use App\Http\Controllers\Veritrans\Veritrans_VtWeb;
use App\Http\Controllers\Veritrans\Veritrans_Sanitizer;

class HandlingPaymentVeritransCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name 		= 'veritrans:notification';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description 	= 'Running payment notification veritrans.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//
		$result 		= $this->handlingnotif();

		return $result;
	}

	/**
	 * 
	 * @return true
	 * @author 
	 **/
	public function handlingnotif()
	{
		// Set our server key
		Veritrans_Config::$serverKey	= env('VERITRANS_KEY', 'VT_KEY');

		// Uncomment for production environment
		Veritrans_Config::$isProduction	= env('VERITRANS_PRODUCTION', false);

		$waiting_transaction			= Sale::status('veritrans_payment_process')->get();

		foreach ($waiting_transaction as $key => $value) 
		{
			$notif 							= new Veritrans_Notification(['transaction_id' => $value['ref_number']]);

			$transaction 					= $notif->transaction_status;

			if($transaction=='settlement')
			{
				$paid_data					= new \App\Models\Payment;
	
				$payment['transaction_id']	= $value['id'];
				$payment['method']			= $notif->payment_type;
				$payment['destination']		= 'Veritrans';
				$payment['account_name']	= $notif->masked_card;
				$payment['account_number']	= $notif->approval_code;
				$payment['ondate']			= \Carbon\Carbon::parse($notif->transaction_time)->format('Y-m-d H:i:s');
				$payment['amount']			= $notif->gross_amount;

				$paid_data					= $paid_data->fill($payment);

				if(!$paid_data->save())
				{
					\Log::error(json_encode($paid_data));

					return false;
				}
			}
		}

		return true;
	}
}
