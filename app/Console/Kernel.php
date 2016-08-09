<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\QueueCommand',
		'App\Console\Commands\PointExpireQueueCommand',
		'App\Console\Commands\PointExpireCommand',
		'App\Console\Commands\BroadcastDiscountCommand',
		'App\Console\Commands\HandlingPaymentVeritransCommand',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		//running queue (every five minutes)
		$schedule->command('run:queue QueueCommand')
				 ->everyFiveMinutes();

		//running queue (every five minutes)
		$schedule->command('point:expirequeue PointExpireQueueCommand')
				 ->dailyAt('06:00');

		//running queue (every five minutes)
		$schedule->command('veritrans:notification HandlingPaymentVeritransCommand')
				 ->everyFiveMinutes();
	}
}
