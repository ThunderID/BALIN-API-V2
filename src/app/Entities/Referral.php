<?php

namespace App\Entities;

use App\Entities\Traits\HasQuotaTrait;
use App\Entities\Traits\IsReferralTrait;

use App\Entities\Traits\GetAllTrait;

use App\CrossServices\ClosedDoorModelObserver;

use App\Entities\TraitRelations\BelongsToUserTrait;

/** 
	* Inheritance Campaign Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Referral extends VoucherCampaign
{
	/**
	 * Traits To Calculated Relations
	 */
	use HasQuotaTrait;
	
	/**
	 * Traits To Condition within itself
	 */
	use IsReferralTrait;

	use GetAllTrait;

	/**
	 * Relationship Traits
	 *
	 */
	use BelongsToUserTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'user_id'						,
											'code'							,
											'type'							,
											'value'							,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'type'							=> 'in:referral',
											'code'							=> 'max:255|min:8',
											'value'							=> 'numeric',
										];
	
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 				= ['user_id'];

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/**
	 * boot
	 * observing model
	 *
	 */
	public static function boot() 
	{
        parent::boot();

        Voucher::observe(new ClosedDoorModelObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
