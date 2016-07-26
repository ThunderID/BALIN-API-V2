<?php

namespace App\Models;

use App\Models\Observers\StoreSettingObserver;

/** 
	* Inheritance StoreSetting Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Store extends StoreSetting
{
	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */
	public $type					=	['url', 'logo', 'facebook_url', 'twitter_url', 'instagram_url', 'email', 'phone', 'address', 'bank_information'];

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
											'type'								=> 'in:url,logo,facebook_url,twitter_url,instagram_url,email,phone,address,bank_information',
											'started_at'						=> 'date_format:"Y-m-d H:i:s"'/*|after: - 1 second'*/,
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
 
        Store::observe(new StoreSettingObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
