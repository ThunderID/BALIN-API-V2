<?php

namespace App\Entities;

use App\CrossServices\ClosedDoorModelObserver;

use App\Entities\TraitLibraries\CustomTimeQueryTrait;

/**
 * Used for StoreSetting, Policy, Store, Page, Slider Models
 * 
 * @author cmooy
 */
class StoreSetting extends BaseModel
{
	/**
	 * Libraries Traits for scopes
	 *
	 */
	use CustomTimeQueryTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'tmp_store_settings';


	/**
	 * Date will be returned as carbon
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'started_at', 'ended_at'];

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
 
        StoreSetting::observe(new ClosedDoorModelObserver());
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

		return $query->where('started_at', '<=', date('Y-m-d H:i:s', strtotime($variable[0])))->where('ended_at', '>=', date('Y-m-d H:i:s', strtotime($variable[1])))->orderBy('started_at', 'asc');
	}
}

