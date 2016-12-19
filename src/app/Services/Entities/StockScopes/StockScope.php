<?php 

namespace App\Services\Entities\StockScopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * to define user role
 *
 * @return Stocks
 * @author cmooy
 */
class StockScope implements ScopeInterface  
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
		if($model->getTable()=='products')
		{
			$builder->selectglobalstock(true)
					->LeftJoinVarianFromProduct(true)
					->LeftJoinTransactionDetailFromVarian(true)
					->LeftJoinTransactionFromTransactionDetail(true)
					->LeftJoinTransactionLogFromTransactionOnStatus(['wait', 'veritrans_processing_payment', 'paid', 'packed', 'shipping', 'delivered'])
					->groupby('products.id')
					;
		}
		else
		{
			if(isset($model->sort))
			{
				$builder->selectglobalstock(true)
						->LeftJoinTransactionDetailFromVarian(true)
						->LeftJoinTransactionFromTransactionDetail(true)
						->LeftJoinTransactionLogFromTransactionOnStatus(['wait', 'veritrans_processing_payment', 'paid', 'packed', 'shipping', 'delivered'])
						->groupby('varians.id')
						->orderby($model->sort, $model->sort_param)
						;
			}
			else
			{
				$builder->selectglobalstock(true)
						->LeftJoinTransactionDetailFromVarian(true)
						->LeftJoinTransactionFromTransactionDetail(true)
						->LeftJoinTransactionLogFromTransactionOnStatus(['wait', 'veritrans_processing_payment', 'paid', 'packed', 'shipping', 'delivered'])
						->groupby('varians.id')
						;
			}
		}
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
