<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class ReferralServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'App\Contracts\Policies\ValidatingReferralSistemInterface', 'App\Services\Policies\ValidatingReferralSistem' );
		$this->app->bind( 'App\Contracts\Policies\ProceedReferralSistemInterface', 'App\Services\Policies\ProceedReferralSistem' );
		$this->app->bind( 'App\Contracts\Policies\EffectReferralSistemInterface', 'App\Services\Policies\EffectReferralSistem' );
	}
}
