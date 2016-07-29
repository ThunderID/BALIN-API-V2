<?php 

namespace App\Services\Entities\PriceScopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use DB;

/**
 * to define user role
 *
 * @return Stocks
 * @author cmooy
 */
class PriceScope implements ScopeInterface  
{
	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return void
	 */
	public function apply(Builder $builder, Model $model)
	{
		$builder
		->selectraw('IFNULL(prices.price, 0) as price')
		->selectraw('IFNULL(prices.promo_price, 0) as promo_price')
		->selectraw('IFNULL(prices.started_at, NOW()) as price_start')
		->leftjoin('prices', function ($join)
		 {
            $join->on ( 'prices.product_id', '=', 'products.id' )
			->on(DB::raw('(prices.id = (select id from prices as tl2 where tl2.product_id = prices.product_id and tl2.deleted_at is null and tl2.started_at <= "'.date('Y-m-d H:i:s').'" order by tl2.started_at desc limit 1))'), DB::raw(''), DB::raw(''))
            ->where('prices.started_at', '<=', date('Y-m-d H:i:s'))
            ->wherenull('prices.deleted_at')
            ;
		});
	}

	/**
	 * Remove the scope from the given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return void
	 */
	public function remove(Builder $builder, Model $model)
	{
	    $query = $builder->getQuery();
	    unset($query);
	}
}
