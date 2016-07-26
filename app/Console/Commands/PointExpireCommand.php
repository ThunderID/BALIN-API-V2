<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\Queue;
use App\Models\PointLog;

use DB, Carbon\Carbon;

use \Illuminate\Support\MessageBag as MessageBag;

class PointExpireCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name 		= 'point:expire';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description 	= 'Running send mail for point expire reminder.';

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
		$id 			= $this->argument()['queueid'];

		$result 		= $this->pointexpire($id);

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
	public function pointexpire($id)
	{
		$queue 						= new Queue;
		$pending 					= $queue->find($id);

		$parameters 				= json_decode($pending->parameter, true);
		$messages 					= json_decode($pending->message, true);

		$errors 					= new MessageBag;

		//check point expire on that day that havent get cut by transaction (or even left over)
		$points 					= PointLog::debit(true)->onactive([Carbon::parse($parameters['on'])->startOfDay()->format('Y-m-d H:i:s'), Carbon::parse($parameters['on'])->endOfDay()->format('Y-m-d H:i:s')])->haventgetcut(true)->with(['user'])->get()->toArray();

		foreach ($points as $idx => $point) 
		{
			//1. Check tag/category viewed
			$stat 					= \App\Models\StatUserView::userid($point['user_id'])->statabletype(['App\Models\Category', 'App\Models\Tag'])->get(['statable_id'])->toArray();

			//1b. Get slugs
			$slugs                      = [];
			$purchased_prods            = [];
			$purchased_varians          = [];
			foreach ($stat as $key => $value) 
			{
				$slugs[]                = \App\Models\Cluster::find($value['statable_id'])['slug'];
			}

			$purchased                  = \App\Models\TransactionDetail::TransactionSellOn(['paid', 'packed', 'shipping', 'delivered'])->where('transactions.user_id', $point['user_id'])->groupby('varian_id')->with(['varian', 'varian.product', 'varian.product.clusters'])->get()->toArray();

			foreach ($purchased as $key => $value) 
			{
				//2. Check tag/category purchased
				foreach ($value['varian']['product']['clusters'] as $key2 => $value2) 
				{
					$slugs[]            = $value2['slug'];
				}

				$purchased_prods[]      = $value['varian']['product_id']; 
				$purchased_varians[]    = $value['varian']['size']; 
			}

			//2a. get slug of category/tag
			//2b. get product id
			//2c. get varian size
			$slug                       = array_unique($slugs);
			$productids                 = array_unique($purchased_prods);
			$variansize                 = array_unique($purchased_varians);

			$result                     = \App\Models\Product::sellable(true);
			if(!empty($slug))
			{
				$result                 = $result->clustersslug($slug);
			}
			if(!empty($productids))
			{
				$result                 = $result->notid($productids);
			}
			if(!empty($variansize))
			{
				$result                 = $result->variansize($variansize);
			}

			$product                     = $result->orderby('price', 'desc')->take(4)->get()->toArray();

			$data						= ['point' => $point, 'balin' => $parameters['store'], 'product' => $product];

			//send mail
			Mail::send('mail.'.$parameters['template'].'.crm.point', ['data' => $data], function($message) use($point, $parameters)
			{
				$message->to($point['user']['email'], $point['user']['name'])->subject(strtoupper($parameters['template']).' - POINT REMINDER');
			}); 

			$pnumber 						= $pending->process_number + 1;
			$messages['message'][$pnumber] 	= 'Sukses Mengirim Email '.(isset($point['user']['name']) ? $point['user']['name'] : '');
			$pending->fill(['process_number' => $pnumber, 'message' => json_encode($messages)]);
			$pending->save();
		}

		return true;
	}
}
