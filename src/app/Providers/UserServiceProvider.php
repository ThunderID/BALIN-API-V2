<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class UserServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'App\Contracts\Policies\ValidatingRegisterUserInterface', 'App\Services\Policies\ValidatingRegisterUser' );
		$this->app->bind( 'App\Contracts\Policies\ProceedRegisterUserInterface', 'App\Services\Policies\ProceedRegisterUser' );
		$this->app->bind( 'App\Contracts\Policies\EffectRegisterUserInterface', 'App\Services\Policies\EffectRegisterUser' );
	}
}
