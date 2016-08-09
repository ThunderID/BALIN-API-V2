<?php

namespace App\Entities;

use App\CrossServices\ClosedDoorModelObserver;

use App\Entities\Traits\HasCurrentAddressTrait;
use App\Entities\Traits\HasImageTrait;

use App\Entities\Traits\GetAllTrait;

use App\Entities\TraitLibraries\FieldNameTrait;

use App\Entities\TraitRelations\HasManyAddressesTrait;
use App\Entities\TraitRelations\HasManyShippingCostsTrait;
use App\Entities\TraitRelations\HasManyShipmentsTrait;
use App\Entities\TraitRelations\MorphManyImagesTrait;

class Courier extends BaseModel
{
	/**
	 * Traits To Calculated Relations
	 */
	use HasCurrentAddressTrait;
	use HasImageTrait;

	/**
	 * Traits To Condition within itself
	 */
	use GetAllTrait;

	/**
	 * Libraries Traits for scopes
	 *
	 */
	use FieldNameTrait;

	/**
	 * Relationship Traits
	 *
	 */
	use HasManyAddressesTrait;
	use HasManyShippingCostsTrait;
	use HasManyShipmentsTrait;
	use MorphManyImagesTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'couriers';
	
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
											'name'							=> 'max:255',
										];
	


	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        Courier::observe(new ClosedDoorModelObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
