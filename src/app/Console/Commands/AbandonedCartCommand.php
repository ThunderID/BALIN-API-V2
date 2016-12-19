<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Entities\Queue;
use App\Entities\Sale;

use DB, Carbon\Carbon;

use \Illuminate\Support\MessageBag as MessageBag;

use App\Contracts\Policies\EffectTransactionInterface;

class AbandonedCartCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name 			= 'cart:abandoned';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description 		= 'Running send mail for abandoned cart reminder.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(EffectTransactionInterface $mail_reminder)
	{
		parent::__construct();

		$this->mail_reminder	= $mail_reminder;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//
		$id 			= $this->argument()['queueid'];

		$result 		= $this->abandonedcart($id);

		return $result;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['queueid', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('queuefunc', null, InputOption::VALUE_OPTIONAL, 'Queue Function', null),
		);
	}

	/**
	 * update 1st version
	 *
	 * @return void
	 * @author 
	 **/
	public function abandonedcart($id)
	{
		$queue 							= new Queue;
		$pending 						= $queue->find($id);

		$parameters 					= json_decode($pending->parameter, true);
		$messages 						= json_decode($pending->message, true);

		$errors 						= new MessageBag;

		//check point expire on that day that havent get cut by transaction (or even left over)
		$sales 							= Sale::TransactionLogChangedAt([$parameters['start'], $parameters['end']])->status(['cart'])->with(['voucher', 'transactionlogs', 'user', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'paidpointlogs', 'payment', 'shipment', 'shipment.address', 'shipment.courier', 'transactionextensions', 'transactionextensions.productextension'])->get();

		foreach ($sales as $idx => $sale) 
		{
			$this->mail_reminder->sendmailabandonedcart($sale);

			$pnumber 						= $pending->process_number + 1;
			$messages['message'][$pnumber] 	= 'Sukses Mengirim Email '.(isset($sale['user']['name']) ? $sale['user']['name'] : '');
			$pending->fill(['process_number' => $pnumber, 'message' => json_encode($messages)]);
			$pending->save();
		}

		return true;
	}
}
