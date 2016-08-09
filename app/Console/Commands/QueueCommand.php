<?php 
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Entities\Queue;

class QueueCommand extends Command 
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'run:queue';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Check Queue (Pending) Jobs.';

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
		//Check Queue
		$result 		= $this->checkpendingjobs();
		
		$this->info("Sukses Simpan \n");

		return true;
	}

	/**
	 * update 1st version
	 *
	 * @return void
	 * @author 
	 **/
	public function checkpendingjobs()
	{
		set_time_limit(0);
		
		$queue 						= new Queue;
		$pendings 					= $queue->running(true)->orderby('updated_at', 'desc')->get();

		if(count($pendings) > 0)
		{
			foreach ($pendings as $key => $value) 
			{
				if($value->process_option!='')
				{
					$check 				= $this->call($value->process_name, ['queueid' => $value->id, '--queuefunc' => $value->process_option]);
				}
				else
				{
					$check 				= $this->call($value->process_name, ['queueid' => $value->id]);
				}
			}			
		}

		return true;

	}
}
