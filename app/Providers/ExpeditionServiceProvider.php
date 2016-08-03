<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class ExpeditionServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'App\Contracts\Policies\ValidatingExpeditionInterface', 'App\Services\Policies\ValidatingExpedition' );
		$this->app->bind( 'App\Contracts\Policies\ProceedExpeditionInterface', 'App\Services\Policies\ProceedExpedition' );
		$this->app->bind( 'App\Contracts\Policies\EffectExpeditionInterface', 'App\Services\Policies\EffectExpedition' );
	}
}
