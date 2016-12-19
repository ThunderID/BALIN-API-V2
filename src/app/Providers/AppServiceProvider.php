<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

// use App\Models\Sale;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( 'App\Contracts\AddToCartInterface', 'App\Services\BalinAddToCart' );
		$this->app->bind( 'App\Contracts\CheckoutInterface', 'App\Services\BalinCheckout' );
		
		// blade extens date indo
		Blade::directive('thunder_mail_date_indo', function($expression)
		{
			return "<?php echo date('d-m-Y H:i', strtotime($expression)); ?>";
		});

		// blade extens money indonesia
		Blade::directive('thunder_mail_money_indo', function($expression)
		{
			return "<?php echo 'IDR '.number_format($expression, 0, ',', '.'); ?>";
		});

		// blade extens money indonesia for email
		Blade::directive('thunder_mail_money_indo_without_IDR', function($expression)
		{
			return "<?php echo number_format($expression, 0, ',', '.'); ?>";
		});
	}
}
