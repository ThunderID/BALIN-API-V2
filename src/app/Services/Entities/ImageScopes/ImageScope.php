<?php 

namespace App\Services\Entities\ImageScopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Scope to get default image
 *
 * @return points
 * @author cmooy
 */
class ImageScope implements ScopeInterface  
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
		->selectraw('images.thumbnail as thumbnail')
		->selectraw('images.image_xs as image_xs')
		->selectraw('images.image_sm as image_sm')
		->selectraw('images.image_md as image_md')
		->selectraw('images.image_lg as image_lg')
		->leftjoin('images', function ($join) use($model)
		 {
            $join->on ( 'images.imageable_id', '=', $model->getTable().'.id' )
            ->whereIn('images.imageable_type', [str_replace('Entities', 'Models', get_class($model)), get_class($model)])
            ->where('images.is_default', '=', true)
            ->wherenull('images.deleted_at')
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
