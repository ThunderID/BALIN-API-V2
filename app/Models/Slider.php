<?php


namespace App\Models;

use App\Models\Observers\StoreSettingObserver;

/** 
	* Inheritance StoreSetting Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Slider extends StoreSetting
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\morphMany\HasImagesTrait;
	use \App\Models\Traits\hasOne\HasImageTrait;

	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */
	public $type					=	'slider';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'type'								,
											'value'								,
											'started_at'						,
											'ended_at'							,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'type'								=> 'in:slider',
											'started_at'						=> 'date_format:"Y-m-d H:i:s"'/*|after: - 1 second'*/,
											'ended_at'							=> 'date_format:"Y-m-d H:i:s"'/*|after: - 1 second'*/,
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
 
        Slider::observe(new StoreSettingObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
