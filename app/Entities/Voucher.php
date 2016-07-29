<?php

namespace App\Entities;

use App\Entities\Traits\HasQuotaTrait;
use App\Entities\Traits\IsVoucherTrait;

use App\Entities\Traits\GetAllTrait;

use App\CrossServices\ClosedDoorModelObserver;

/** 
	* Inheritance Campaign Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Voucher extends VoucherCampaign
{
	/**
	 * Traits To Calculated Relations
	 */
	use HasQuotaTrait;
	
	/**
	 * Traits To Condition within itself
	 */
	use IsVoucherTrait;

	use GetAllTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'code'							,
											'type'							,
											'value'							,
											'started_at'					,
											'expired_at'					,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'type'							=> 'in:free_shipping_cost,debit_point,promo_referral',
											'code'							=> 'max:255|min:8',
											'type'							=> 'max:255',
											'value'							=> 'numeric',
											'started_at'					=> 'date_format:"Y-m-d H:i:s"'/*|after:now'*/,
											'expired_at'					=> 'date_format:"Y-m-d H:i:s"|after:now',
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
