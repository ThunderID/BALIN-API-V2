<?php

namespace App\Models;

// use App\Models\Observers\StatUserViewObserver;

/**
 * Future Feature of Stat View
 * 
 * @author cmooy
 */
class StatUserView extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasUserTrait;
 	use \App\Models\Traits\morphTo\HasStatableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'stat_user_views';

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
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'ondate'];

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
											'user_id'						,
											'statable_id'					,
											'statable_type'					,
											'view'							,
											'ondate'						,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'ondate'						=> 'date_format:"Y-m-d H:i:s"',
											'view'							=> 'numeric',
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        // StatUserView::observe(new StatUserViewObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope to find exact date
	 *
	 * @param string of history
	 */
	public  function scopeOndate($query, $variable)
	{
		if(!is_array($variable))
		{
			return $query->where('ondate', '=', date('Y-m-d', strtotime($variable)))->orderBy('ondate', 'desc');
		}

		return $query->where('ondate', '>=', date('Y-m-d', strtotime($variable[0])))->where('ondate', '<=', date('Y-m-d', strtotime($variable[1])))->orderBy('started_at', 'asc');
	}
}
