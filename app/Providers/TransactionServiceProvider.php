<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class TransactionServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'App\Contracts\Policies\ValidatingTransactionInterface', 'App\Services\Policies\ValidatingTransaction' );
		$this->app->bind( 'App\Contracts\Policies\ProceedTransactionInterface', 'App\Services\Policies\ProceedTransaction' );
		$this->app->bind( 'App\Contracts\Policies\EffectTransactionInterface', 'App\Services\Policies\EffectTransaction' );
	}
}
