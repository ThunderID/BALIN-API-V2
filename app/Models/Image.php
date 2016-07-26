<?php

namespace App\Models;

use App\Models\Observers\ImageObserver;

/**
 * Used for Image Models
 * 
 * @author cmooy
 */
class Image extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
 	use \App\Models\Traits\morphTo\HasImageableTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'images';

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
											'imageable_id'					,
											'imageable_type'				,
											'thumbnail'						,
											'image_xs'						,
											'image_sm'						,
											'image_md'						,
											'image_lg'						,
											'is_default'					,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'thumbnail'						=> 'max:255|url',
											'image_xs'						=> 'max:255|url',
											'image_sm'						=> 'max:255|url',
											'image_md'						=> 'max:255|url',
											'image_lg'						=> 'max:255|url',
											'is_default'					=> 'boolean',
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
 
        Image::observe(new ImageObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
