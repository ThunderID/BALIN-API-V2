<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class StoreSettingServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'App\Contracts\Policies\ValidatingStoreSettingInterface', 'App\Services\Policies\ValidatingStoreSetting' );
		$this->app->bind( 'App\Contracts\Policies\ProceedStoreSettingInterface', 'App\Services\Policies\ProceedStoreSetting' );
		$this->app->bind( 'App\Contracts\Policies\EffectStoreSettingInterface', 'App\Services\Policies\EffectStoreSetting' );
	}
}
