<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class OrnamentServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'App\Contracts\Policies\ValidatingOrnamentInterface', 'App\Services\Policies\ValidatingOrnament' );
		$this->app->bind( 'App\Contracts\Policies\ProceedOrnamentInterface', 'App\Services\Policies\ProceedOrnament' );
		$this->app->bind( 'App\Contracts\Policies\EffectOrnamentInterface', 'App\Services\Policies\EffectOrnament' );
	}
}
