<?php

namespace App\Models;

use App\Models\Traits\HasTypeTrait;
use App\Models\Traits\HasSelectAllTrait;
use App\Models\Observers\StoreSettingObserver;
use DB;

/**
 * Used for StoreSetting, Policy, Store, Page, Slider Models
 * 
 * @author cmooy
 */
class StoreSetting extends BaseModel
{
	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasSelectAllTrait;
	use HasTypeTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'tmp_store_settings';

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
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'started_at', 'ended_at'];

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
 
        StoreSetting::observe(new StoreSettingObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
	
	/**
	 * scope to find type of store setting
	 *
	 * @param string of type
	 */
	public function scopeType($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn('type', $variable);
		}

		return 	$query->where('type', $variable);
	}
	
	/**
	 * scope to find history of date
	 *
	 * @param string of history
	 */
	public  function scopeOndate($query, $variable)
	{
		if(!is_array($variable))
		{
			return $query->where('started_at', '<=', date('Y-m-d H:i:s', strtotime($variable)))->orderBy('started_at', 'desc');
		}

		if(!strtotime($variable[0]) && strtotime($variable[1]))
		{
			return $query->where(function ($query) use($variable)
					    	{
							    $query->wherenull('ended_at')
							    ->orwhere('ended_at', '>=',date('Y-m-d H:i:s', strtotime($variable[1])));
							})
			;
		}

		return $query->where('started_at', '>=', date('Y-m-d H:i:s', strtotime($variable[0])))->where('ended_at', '<=', date('Y-m-d H:i:s', strtotime($variable[1])))->orderBy('started_at', 'asc');
	}

	/**
	 * scope to find history of date
	 *
	 * @param string of history
	 */
	public  function scopeDefault($query, $variable)
	{
		return $query->whereraw(DB::raw('tmp_store_settings.id = (select id from tmp_store_settings as tl2 where tl2.type = tmp_store_settings.type and tl2.deleted_at is null order by tl2.started_at desc limit 1)')) ;
	}
}

