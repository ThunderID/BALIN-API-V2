<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class ProductServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'App\Contracts\Policies\ValidatingProductInterface', 'App\Services\Policies\ValidatingProduct' );
		$this->app->bind( 'App\Contracts\Policies\ProceedProductInterface', 'App\Services\Policies\ProceedProduct' );
		$this->app->bind( 'App\Contracts\Policies\EffectProductInterface', 'App\Services\Policies\EffectProduct' );
	}
}
