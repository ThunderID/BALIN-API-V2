<?php

namespace App\Models;

use App\Models\Observers\TransactionLogObserver;
use App\Models\Traits\Changes\HasStatusLogTrait;
use App\Models\Traits\Calculations\HasPaidTrait;

/**
 * Used for TransactionLog Models
 * 
 * @author cmooy
 */
class TransactionLog extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasTransactionTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasStatusLogTrait;
	use HasPaidTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'transaction_logs';

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
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'changed_at'];

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
											'transaction_id'				,
											'status'						,
											'changed_at'					,
											'notes'							,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'transaction_id'				=> 'exists:transactions,id',
											'status'						=> 'in:cart,wait,veritrans_processing_payment,paid,packed,shipping,delivered,canceled,abandoned',
											'changed_at'					=> 'date_format:"Y-m-d H:i:s"',
											'notes'							=> 'required_if:status,delivered',
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
 
        // TransactionLog::observe(new TransactionLogObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
