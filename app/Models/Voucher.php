<?php


namespace App\Models;

use App\Models\Observers\VoucherObserver;

/** 
	* Inheritance Campaign Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Voucher extends Campaign
{
	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */

	public $type					=	['free_shipping_cost', 'debit_point', 'promo_referral'];

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
 
        Voucher::observe(new VoucherObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope to find code of voucher
	 *
	 * @param string of code
	 */
	public function scopeCode($query, $variable)
	{
		return 	$query->where('code', $variable);
	}

	/**
	 * scope to find history of date
	 *
	 * @param string of history
	 */
	public function scopeOnDate($query, $variable)
	{
		if(is_array($variable))
		{
			$started_at 	= date('Y-m-d H:i:s', strtotime($variable[0]));
			$expired_at 	= date('Y-m-d H:i:s', strtotime($variable[1]));

			return $query->where('started_at', '<=', $started_at)
						->where('expired_at', '>=', $expired_at)
						;
		}
		else
		{
			$ondate 	= date('Y-m-d H:i:s', strtotime($variable));
			return $query->where('started_at', '<=', $ondate)
						->where('expired_at', '>=', $ondate)
						;
		}
	}
	
	/**
	 * scope to find type of voucher
	 *
	 * @param string of type
	 */
	public function scopeType($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn('type', $variable);
		}

		return 	$query->where('type', $variable);
	}
}
