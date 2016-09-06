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
		'App\Console\Commands\AbandonedCartQueueCommand',
		'App\Console\Commands\AbandonedCartCommand',
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
		$schedule->command('run:queue')
				 ->everyFiveMinutes();

		//running queue (every day)
		$schedule->command('point:expirequeue')
				 ->dailyAt('06:00');

		//running queue (every day)
		$schedule->command('cart:abandonedqueue')
				 ->dailyAt('07:00');

		//running queue (every five minutes)
		$schedule->command('veritrans:notification')
				 ->everyFiveMinutes();
	}
}
