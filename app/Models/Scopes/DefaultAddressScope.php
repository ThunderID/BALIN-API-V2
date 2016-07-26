<?php namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Scope to get default address (is_default is true)
 *
 * @return thumbnail, address_xs, address_sm, address_md, address_lg
 * @author cmooy
 */

class DefaultAddressScope implements ScopeInterface  
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
		->selectraw('addresses.address as current_address')
		->selectraw('addresses.zipcode as current_zipcode')
		->selectraw('addresses.phone as current_phone')
		->leftjoin('addresses', function ($join) use($model)
		 {
            $join->on ( 'addresses.owner_id', '=', $model->getTable().'.id' )
            ->where('addresses.owner_type', '=', get_class($model))
            ->wherenull('addresses.deleted_at')
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
	    // unset($query);
	}
}
