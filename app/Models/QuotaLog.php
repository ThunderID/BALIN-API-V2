<?php

namespace App\Models;

use App\Models\Observers\QuotaLogObserver;

/**
 * Used for QuotaLog Models
 * 
 * @author cmooy
 */
class QuotaLog extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasVoucherTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'quota_logs';

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
											'voucher_id'					,
											'amount'						,
											'notes'							,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'voucher_id'					=> 'exists:tmp_vouchers,id',
											'amount'						=> 'numeric',
										];
	
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
 
        QuotaLog::observe(new QuotaLogObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
	
	/**
	 * scope to find history of date
	 *
	 * @param string of history
	 */
	public  function scopeOndate($query, $variable)
	{
		if(!is_array($variable))
		{
			return $query->where('created_at', '<=', date('Y-m-d H:i:s', strtotime($variable)))->orderBy('created_at', 'desc');
		}

		if(!strtotime($variable[1]) && strtotime($variable[0]))
		{
			return $query->where('created_at', '<=',date('Y-m-d H:i:s', strtotime($variable[0])));
		}

		return $query->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($variable[0])))->where('created_at', '<=', date('Y-m-d H:i:s', strtotime($variable[1])))->orderBy('created_at', 'asc');
	}
}
