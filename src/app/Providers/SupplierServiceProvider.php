<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class SupplierServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'App\Contracts\Policies\ValidatingSupplierInterface', 'App\Services\Policies\ValidatingSupplier' );
		$this->app->bind( 'App\Contracts\Policies\ProceedSupplierInterface', 'App\Services\Policies\ProceedSupplier' );
		$this->app->bind( 'App\Contracts\Policies\EffectSupplierInterface', 'App\Services\Policies\EffectSupplier' );
	}
}
