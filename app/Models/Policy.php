<?php

namespace App\Models;

use App\Models\Observers\PolicyObserver;

/** 
	* Inheritance StoreSetting Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Policy extends StoreSetting
{
	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */
	public $type					=	['expired_cart', 'expired_paid', 'expired_shipped', 'expired_point', 'referral_royalty', 'invitation_royalty', 'limit_unique_number', 'expired_link_duration', 'first_quota', 'downline_purchase_bonus', 'downline_purchase_bonus_expired', 'downline_purchase_quota_bonus', 'voucher_point_expired', 'welcome_gift', 'critical_stock', 'min_margin', 'item_for_one_package'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'type'								,
											'value'								,
											'started_at'						,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'type'								=> 'in:expired_cart,expired_paid,expired_shipped,expired_point,referral_royalty,invitation_royalty,limit_unique_number,expired_link_duration,first_quota,downline_purchase_bonus,downline_purchase_bonus_expired,downline_purchase_quota_bonus,voucher_point_expired,welcome_gift,critical_stock,min_margin,item_for_one_package',
											'started_at'						=> 'date_format:"Y-m-d H:i:s"'/*|after: - 1 second'*/,
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
 
        Policy::observe(new PolicyObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
