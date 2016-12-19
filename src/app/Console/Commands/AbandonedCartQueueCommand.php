<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Entities\Sale;
use App\Entities\Queue;
use App\Entities\Policy;

use Log, DB, Carbon\Carbon;

class AbandonedCartQueueCommand extends Command 
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cart:abandonedqueue';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate queue for abandoned cart.';

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
		$result 		= $this->generate();
		
		return true;
	}

	/**
	 * generate queue log
	 *
	 * @return void
	 * @author 
	 **/
	public function generate()
	{
		Log::info('Running AbandonedCartQueue Generator command @'.date('Y-m-d H:i:s'));

		$policies 						= new Policy;
		$policies 						= $policies->default(true)->get()->toArray();

		$store 							= [];
		foreach ($policies as $key => $value2) 
		{
			$store[$value2['type']]		= $value2['value'];
		}

		$expired_today 					= Sale::TransactionLogChangedAt([Carbon::parse($store['expired_paid'])->startOfDay()->format('Y-m-d H:i:s'), Carbon::parse($store['expired_paid'])->endOfDay()->format('Y-m-d H:i:s')])->status(['cart'])->count();

		if($expired_today > 0)
		{
			DB::beginTransaction();

			$parameter['store']				= $store;
			$parameter['template']			= 'balin';
			$parameter['start']				= Carbon::parse($store['expired_paid'])->startOfDay()->format('Y-m-d H:i:s');
			$parameter['end']				= Carbon::parse($store['expired_paid'])->endOfDay()->format('Y-m-d H:i:s');

			$queue 							= new Queue;
			$queue->fill([
					'process_name' 			=> 'cart:abandoned',
					'parameter' 			=> json_encode($parameter),
					'total_process' 		=> $expired_today,
					'task_per_process' 		=> 1,
					'process_number' 		=> 0,
					'total_task' 			=> $expired_today,
					'message' 				=> 'Initial Commit',
			]);

			if(!$queue->save())
			{
				DB::rollback();

				Log::error('Save queue on PointExpireQueue command '.json_encode($queue->getError()));
			}
			else
			{
				DB::Commit();
			}
		}
	}
}
