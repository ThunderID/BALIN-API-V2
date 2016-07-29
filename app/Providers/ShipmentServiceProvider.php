<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class ShipmentServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'App\Contracts\Policies\ValidatingShipmentInterface', 'App\Services\Policies\ValidatingShipment' );
		$this->app->bind( 'App\Contracts\Policies\ProceedShipmentInterface', 'App\Services\Policies\ProceedShipment' );
		$this->app->bind( 'App\Contracts\Policies\EffectShipmentInterface', 'App\Services\Policies\EffectShipment' );
	}
}
