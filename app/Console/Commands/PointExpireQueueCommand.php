<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\PointLog;
use App\Models\Queue;
use App\Models\Store;
use App\Models\ClientTemplate;

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
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['example', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

	/**
	 * absence log
	 *
	 * @return void
	 * @author 
	 **/
	public function generate()
	{
		Log::info('Running PointExpireQueue Generator command @'.date('Y-m-d H:i:s'));

		$clients 								= ClientTemplate::get();

		foreach ($clients as $key => $value) 
		{
			$points 							= PointLog::debit(true)->onactive([Carbon::parse(' + 1 month')->startOfDay()->format('Y-m-d H:i:s'), Carbon::parse(' + 1 month')->endOfDay()->format('Y-m-d H:i:s')])->haventgetcut(true)->get();

			if(count($points) > 0)
			{
				$policies 						= new Store;
				$policies 						= $policies->default(true)->get()->toArray();

				$store 							= [];
				foreach ($policies as $key => $value2) 
				{
					$store[$value2['type']]		= $value2['value'];
				}
				$store['action'] 				= $store['url'].'/product';

				DB::beginTransaction();

				$parameter['store']				= $store;
				$parameter['template']			= $value['located'];
				$parameter['on']				= Carbon::parse(' + 1 month')->format('Y-m-d H:i:s');

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
		}

		return true;
	}

}
