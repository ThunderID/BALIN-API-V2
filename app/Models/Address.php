<?php

namespace App\Models;

use App\Models\Observers\AddressObserver;

/**
 * Used for Address Models
 * 
 * @author cmooy
 */
class Address extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\hasMany\HasShipmentsTrait;
 	use \App\Models\Traits\morphTo\HasOwnerTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'addresses';

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
											'owner_id'						,
											'owner_type'					,
											'phone'							,
											'address'						,
											'zipcode'						,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'phone'							=> 'max:255',
											'zipcode'						=> 'max:255',
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
 
        Address::observe(new AddressObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope to get condition where phone
	 *
	 * @param string of phone
	 **/
	public function scopePhone($query, $variable)
	{
		return 	$query->where($query->getModel()->table.'.phone', $variable);
	}

	/**
	 * scope to get condition where address
	 *
	 * @param string of address
	 **/
	public function scopeAddress($query, $variable)
	{
		return 	$query->where($query->getModel()->table.'.address', 'like', '%'.$variable.'%');
	}

	/**
	 * scope to get condition where zipcode
	 *
	 * @param string of zipcode
	 **/
	public function scopeZipcode($query, $variable)
	{
		return 	$query->where($query->getModel()->table.'.zipcode', $variable);
	}
}
