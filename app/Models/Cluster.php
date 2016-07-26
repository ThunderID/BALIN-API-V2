<?php

namespace App\Models;

use App\Models\Observers\ClusterObserver;

use App\Models\Traits\HasTypeTrait;
use App\Models\Traits\HasStockTrait;
use App\Models\Traits\HasNameTrait;
use App\Models\Traits\HasSlugTrait;
use App\Models\Traits\HasTransactionStatusTrait;
use \App\Models\Traits\HasStatableTrait;

/**
 * Used for Category and Tag Models
 * 
 * @author cmooy
 */
class Cluster extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasClusterTrait;
	use \App\Models\Traits\belongsToMany\HasProductsTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasTypeTrait;
	use HasStockTrait;
	use HasNameTrait;
	use HasSlugTrait;
	use HasTransactionStatusTrait;

	/**
	 * Global traits used as scope (plugged scope).
	 *
	 */
	use HasStatableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'categories';

	/**
	 * Timestamp field
	 *
	 * @var array
	 */
	// protected $timestamps			= true;
	
	/**
	 * Date will be returned as carbon
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'transact_at'];

	/**
	 * The appends attributes from mutator and accessor
	 *
	 * @var array
	 */
	protected $appends				=	[];

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
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
	
	/**
	 * boot
	 * observing model
	 *
	 */
	public static function boot() 
	{
        parent::boot();
 
        Cluster::observe(new ClusterObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
