<?php

namespace App\Models;

use App\Models\Traits\HasDefaultImageTrait;
use App\Models\Traits\HasDefaultAddressTrait;
use App\Models\Traits\HasNameTrait;
use App\Models\Traits\HasSelectAllTrait;

use App\Models\Observers\CourierObserver;

/**
 * Used for Courier Models
 * 
 * @author cmooy
 */
class Courier extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\hasMany\HasShipmentsTrait;
	use \App\Models\Traits\hasMany\HasShippingCostsTrait;
	use \App\Models\Traits\hasMany\HasAddressesTrait;
	
	use \App\Models\Traits\morphMany\HasImagesTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasSelectAllTrait;
	use HasDefaultAddressTrait;
	use HasDefaultImageTrait;

	/**
	 * Global traits used as query builder (plugged scope).
	 *
	 */
	use HasNameTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'couriers';

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
	protected $dates				=	['created_at', 'updated_at', 'deleted_at'];

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

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'name'							,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'name'							=> 'required|max:255',
										];
	
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
 
        Courier::observe(new CourierObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
