<?php

namespace App\Entities;

use App\CrossServices\ClosedDoorModelObserver;

class PointLog extends BaseModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'point_logs';
	
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
											'user_id'						,
											'point_log_id'					,
											'reference_id'					,
											'reference_type'				,
											'amount'						,
											'expired_at'					,
											'notes'							,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'user_id'						=> 'exists:users,id',
											'amount'						=> 'numeric',
											'expired_at'					=> 'date_format:"Y-m-d H:i:s"|after:now',
										];


	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        PointLog::observe(new ClosedDoorModelObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope to find not expired point
	 *
	 * @param string or array of expired
	 */
	public  function scopeOnActive($query, $variable)
	{
		if(!is_array($variable))
		{
			return $query->where('expired_at', '>=', date('Y-m-d H:i:s', strtotime($variable)));
		}

		return $query->where('expired_at', '>=', date('Y-m-d H:i:s', strtotime($variable[0])))->where('expired_at', '<=', date('Y-m-d H:i:s', strtotime($variable[1])));
	}

	/**
	 * scope to find debit amount for user
	 *
	 * @param none
	 */
	public  function scopeDebit($query, $variable)
	{
		return $query->where('amount', '>', 0);
	}

	/**
	 * scope to find point summary for user
	 *
	 * @param user id
	 */
	public function scopeSummary($query, $variable)
	{
		return 	$query->selectraw('point_logs.*')
						->selectraw('IFNULL(SUM(point_logs.amount),0) as amount')
						->selectraw('(SELECT IFNULL(SUM(plogs.amount),0) FROM point_logs as plogs where user_id = '.$variable.' and point_logs.created_at > plogs.created_at and plogs.deleted_at is null and plogs.expired_at > NOW()) as prev_amount')
						->userid($variable)
						->groupby(['point_logs.reference_type', 'point_logs.reference_id'])
		;
	}
}
