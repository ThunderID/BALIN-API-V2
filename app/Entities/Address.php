<?php

namespace App\Entities;

use App\CrossServices\ClosedDoorModelObserver;

use App\Entities\TraitRelations\MorphToOwnerTrait;

class Address extends BaseModel
{
	/**
	 * Relationship Traits
	 *
	 */
	use MorphToOwnerTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'addresses';
	
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
		
	public static function boot() 
	{
        parent::boot();
 
        Address::observe(new ClosedDoorModelObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope to get condition where Phone
	 *
	 * @param string or array of entity' Phone
	 **/
	public function scopePhone($query, $variable)
	{
		return 	$query->where('Phone', $variable);
	}

	/**
	 * scope to get condition where Address
	 *
	 * @param string or array of entity' Address
	 **/
	public function scopeAddress($query, $variable)
	{
		return 	$query->where('Address', $variable);
	}

	/**
	 * scope to get condition where Zipcode
	 *
	 * @param string or array of entity' Zipcode
	 **/
	public function scopeZipcode($query, $variable)
	{
		return 	$query->where('Zipcode', $variable);
	}

}
