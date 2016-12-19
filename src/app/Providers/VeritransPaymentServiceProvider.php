<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class VeritransPaymentServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'App\Contracts\ProcessingPaymentInterface', 'App\Services\VeritransProcessingPayment' );
	}
}
