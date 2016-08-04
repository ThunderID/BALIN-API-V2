<?php

namespace App\Entities;

use App\Entities\Traits\IsTagTrait;

use App\Entities\TraitLibraries\FieldNameTrait;
use App\Entities\TraitLibraries\FieldSlugTrait;

use App\Entities\TraitRelations\BelongsToCategoryClusterTrait;
use App\Entities\TraitRelations\BelongsToManyProductsTrait;

use App\Entities\Observers\ClusterObserver;

/** 
	* Inheritance Tag Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Tag extends CategoryCluster
{
	/**
	 * Traits To Condition within itself
	 */
	use IsTagTrait;

	/**
	 * Libraries Traits for scopes
	 *
	 */
	use FieldNameTrait;
	use FieldSlugTrait;
	
	/**
	 * Relationship Traits
	 *
	 */
	use BelongsToCategoryClusterTrait;
	use BelongsToManyProductsTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'category_id'					,
											'type'							,
											'path'							,
											'name'							,
											'slug'							,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'path'							=> 'max:255',
											'name'							=> 'max:255',
											'slug'							=> 'max:255',
										];
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 				= [];

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/**
	 * boot
	 * observing model
	 *
	 */
	public static function boot() 
	{
        parent::boot();

        Tag::observe(new ClusterObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
