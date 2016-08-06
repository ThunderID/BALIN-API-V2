<?php

namespace App\Entities;

use App\Entities\Traits\HasImageTrait;

use App\Entities\Traits\GetAllTrait;

use App\Entities\Traits\IsBannerTrait;

use App\CrossServices\ClosedDoorModelObserver;

use App\Entities\TraitRelations\HasOneImageTrait;
use App\Entities\TraitRelations\MorphManyImagesTrait;

/** 
	* Inheritance StoreSetting Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Banner extends StoreSetting
{
	/**
	 * Traits To Calculated Relations
	 */
	use HasImageTrait;
	
	/**
	 * Traits To Condition within itself
	 */
	use IsBannerTrait;

	use GetAllTrait;

	/**
	 * Relationship Traits
	 *
	 */
	use HasOneImageTrait;
	use MorphManyImagesTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'type'								,
											'value'								,
											'started_at'						,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'type'								=> 'in:left_banner,right_banner,full_banner',
											'started_at'						=> 'date_format:"Y-m-d H:i:s"'/*|after: - 1 second'*/,
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
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
	
	/**
	 * boot
	 * observing model
	 *
	 */		
	public static function boot() 
	{
        parent::boot();
 
        Banner::observe(new ClosedDoorModelObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
