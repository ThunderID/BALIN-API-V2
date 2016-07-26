<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class PaymentServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'App\Contracts\Policies\ValidatingPaymentInterface', 'App\Services\Policies\ValidatingPayment' );
		$this->app->bind( 'App\Contracts\Policies\ProceedPaymentInterface', 'App\Services\Policies\ProceedPayment' );
		$this->app->bind( 'App\Contracts\Policies\EffectPaymentInterface', 'App\Services\Policies\EffectPayment' );
	}
}
