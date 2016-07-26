<?php

namespace App\Models;

use App\Models\Traits\HasTypeTrait;
use App\Models\Traits\HasSelectAllTrait;
use App\Models\Traits\HasQuotaTrait;
// use App\Models\Observers\CampaignObserver;

/**
 * Used for Voucher and Referral Models
 * 
 * @author cmooy
 */
class Campaign extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasUserTrait;
	use \App\Models\Traits\hasMany\HasQuotaLogsTrait;
	use \App\Models\Traits\hasMany\HasTransactionsTrait;
	
	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasSelectAllTrait;
	use HasQuotaTrait;
	use HasTypeTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'tmp_vouchers';

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
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'started_at', 'expired_at'];

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
	 *
	 */	
	public static function boot() 
	{
        parent::boot();
 
        // Campaign::observe(new CampaignObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * find code
	 * 
	 * @param code
	 */	
	public function scopeCode($query, $variable)
	{
		return $query->where('code', $variable);
	}

	/**
	 * find range
	 * 
	 * @param array or singular date
	 */	
	public function scopeOnDate($query, $variable)
	{
		if(is_array($variable))
		{
			$started_at 	= date('Y-m-d H:i:s', strtotime($variable[0]));
			$expired_at 	= date('Y-m-d H:i:s', strtotime($variable[1]));
			return $query->where('started_at', '<=', $started_at)
						->where('expired_at', '>=', $expired_at)
						;
		}
		else
		{
			$ondate 	= date('Y-m-d H:i:s', strtotime($variable));
			return $query->where('started_at', '<=', $ondate)
						->where('expired_at', '>=', $ondate)
						;
		}
	}
	
	/**
	 * scope to find type of campaign
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
	
}
