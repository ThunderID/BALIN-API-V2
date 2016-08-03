<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class VoucherServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'App\Contracts\Policies\ValidatingVoucherInterface', 'App\Services\Policies\ValidatingVoucher' );
		$this->app->bind( 'App\Contracts\Policies\ProceedVoucherInterface', 'App\Services\Policies\ProceedVoucher' );
		$this->app->bind( 'App\Contracts\Policies\EffectVoucherInterface', 'App\Services\Policies\EffectVoucher' );
	}
}
