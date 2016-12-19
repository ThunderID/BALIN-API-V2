<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use DB, Carbon\Carbon;

use \Illuminate\Support\MessageBag as MessageBag;

use App\Entities\Sale;
use App\Http\Controllers\Veritrans\Veritrans_Config;
use App\Http\Controllers\Veritrans\Veritrans_Transaction;
use App\Http\Controllers\Veritrans\Veritrans_ApiRequestor;
use App\Http\Controllers\Veritrans\Veritrans_Notification;
use App\Http\Controllers\Veritrans\Veritrans_VtDirect;
use App\Http\Controllers\Veritrans\Veritrans_VtWeb;
use App\Http\Controllers\Veritrans\Veritrans_Sanitizer;

use App\Services\VeritransHandlingPayment;

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
	public function __construct(VeritransHandlingPayment $veritrans_handler)
	{
		parent::__construct();

		$this->veritrans_handler 	= $veritrans_handler;
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

		$waiting_transaction			= Sale::status('veritrans_processing_payment')->with(['voucher', 'transactionlogs', 'customer', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'paidpointlogs', 'payment', 'shipment', 'shipment.address', 'shipment.courier', 'transactionextensions', 'transactionextensions.productextension'])->get();

		foreach ($waiting_transaction as $key => $value) 
		{
			$notif 								= new Veritrans_Notification(['transaction_id' => $value['ref_number']]);

			$sale 								= $value->toArray();
			$sale['payment']['id']				= '';
			$sale['payment']['transaction_id']	= $sale['id'];
			$sale['payment']['method']			= $notif->payment_type;
			$sale['payment']['destination']		= 'Veritrans';
			$sale['payment']['account_name']	= $notif->masked_card;
			$sale['payment']['account_number']	= $notif->approval_code;
			$sale['payment']['ondate']			= \Carbon\Carbon::parse($notif->transaction_time)->format('Y-m-d H:i:s');
			$sale['payment']['amount']			= $notif->gross_amount;
			$sale['payment']['status']			= $notif->transaction_status;

			$sale_store 						= $this->veritrans_handler;

			$sale_store->fill($sale);

			$sale_store->save();
		}

		return true;
	}
}
