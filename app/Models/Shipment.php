<?php

namespace App\Models;

use App\Models\Observers\ShipmentObserver;
use App\Models\Traits\Calculations\HasShipCostTrait;
use App\Models\Traits\Changes\HasStatusLogTrait;

/**
 * Used for Shipment Models
 * 
 * @author cmooy
 */
class Shipment extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasAddressTrait;
	use \App\Models\Traits\belongsTo\HasCourierTrait;
	use \App\Models\Traits\belongsTo\HasTransactionTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasShipCostTrait;
	use HasStatusLogTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'shipments';

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
											'courier_id'					,
											'transaction_id'				,
											'address_id'					,
											'receipt_number'				,
											'receiver_name'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'transaction_id'				=> 'exists:transactions,id',
											'courier_id'					=> 'exists:couriers,id',
											'receipt_number'				=> 'max:255',
											'receiver_name'					=> 'max:255',
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        Shipment::observe(new ShipmentObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
