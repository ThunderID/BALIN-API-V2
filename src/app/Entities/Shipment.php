<?php

namespace App\Entities;

use App\CrossServices\ClosedDoorModelObserver;

use App\Entities\TraitRelations\BelongsToAddressTrait;
use App\Entities\TraitRelations\BelongsToCourierTrait;
use App\Entities\TraitRelations\BelongsToTransactionTrait;

class Shipment extends BaseModel
{
	/**
	 * Relationship Traits
	 *
	 */
	use BelongsToAddressTrait;
	use BelongsToCourierTrait;
	use BelongsToTransactionTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'shipments';
	
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
 
        Shipment::observe(new ClosedDoorModelObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope to get condition where receipt number
	 *
	 * @param string or array of entity' receipt number
	 **/
	public function scopeReceiptNumber($query, $variable)
	{
		return 	$query->where($query->getModel()->table.'.receipt_number', $variable);
	}
}
