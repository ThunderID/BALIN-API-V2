<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Entities\PointLog;
use App\Entities\Queue;
use App\Entities\Store;

use Log, DB, Carbon\Carbon;

class PointExpireQueueCommand extends Command 
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'point:expirequeue';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate queue for point expire.';

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
		Log::info('Running PointExpireQueue Generator command @'.date('Y-m-d H:i:s'));

		$points 							= PointLog::debit(true)->onactive([Carbon::parse(' + 1 month ')->startOfDay()->format('Y-m-d H:i:s'), Carbon::parse(' + 1 month ')->endOfDay()->format('Y-m-d H:i:s')])->haventgetcut(true)->get();

		if(count($points) > 0)
		{
			$policies 						= new Store;
			$policies 						= $policies->default(true)->get()->toArray();

			$store 							= [];
			foreach ($policies as $key => $value2) 
			{
				$store[$value2['type']]		= $value2['value'];
			}

			$store['action'] 				= $store['url'];

			DB::beginTransaction();

			$parameter['store']				= $store;
			$parameter['template']			= 'balin';
			$parameter['on']				= Carbon::parse(' + 1 month ')->format('Y-m-d H:i:s');

			$queue 							= new Queue;
			$queue->fill([
					'process_name' 			=> 'point:expire',
					'parameter' 			=> json_encode($parameter),
					'total_process' 		=> count($points),
					'task_per_process' 		=> 1,
					'process_number' 		=> 0,
					'total_task' 			=> count($points),
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

		return true;
	}

}
