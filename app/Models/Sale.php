<?php

namespace App\Models;

use App\Models\Traits\HasBillAmountTrait;
use App\Models\Traits\HasPointDiscountTrait;
use App\Models\Traits\HasExtendAmountTrait;
use App\Models\Traits\HasProductExtendTrait;
use App\Models\Traits\HasAddressExtendTrait;
use App\Models\Traits\Calculations\HasVoucherQuotaTrait;
use App\Models\Observers\SaleObserver;
use App\Models\Observers\ClosedDoorModelObserver;

/** 
	* Inheritance Transaction Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Sale extends Transaction
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasUserTrait;
	use \App\Models\Traits\belongsTo\HasVoucherTrait;
	use \App\Models\Traits\hasOne\HasPaymentTrait;
	use \App\Models\Traits\hasOne\HasShipmentTrait;
	use \App\Models\Traits\hasMany\HasPointLogsTrait;
	use \App\Models\Traits\hasMany\HasTransactionExtensionsTrait;
	
	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasPointDiscountTrait;
	use HasBillAmountTrait;
	use HasExtendAmountTrait;
	use HasVoucherQuotaTrait;
	
	/**
	 * Global traits used as query builder (plugin scope).
	 *
	 */
	use HasProductExtendTrait;
	use HasAddressExtendTrait;

	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */

	public $type					=	'sell';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'user_id'						,
											'voucher_id'					,
											'ref_number'					,
											'type'							,
											'transact_at'					,
											'unique_number'					,
											'shipping_cost'					,
											'voucher_discount'				,
										];
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'user_id'						=> 'exists:users,id',
											'type'							=> 'in:sell',
											'ref_number'					=> 'max:255',
											// 'transact_at'					=> 'date_format:"Y-m-d H:i:s"',
											'unique_number'					=> 'numeric',
											'shipping_cost'					=> 'numeric',
											'voucher_discount'				=> 'numeric',
										];
	
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
 
        // Sale::observe(new SaleObserver());
        Sale::observe(new ClosedDoorModelObserver());
    }

	/**
	 * generate unique number
	 * 
	 * @param model of sale
	 * @return unique_number
	 */	
    public function generateUniqueNumber($sale)
	{
		if(!is_null($sale['unique_number']))
        {
            $i                          = 0;
            $amount                     = true;

            while($amount)
            {
                $prev_number            = Sale::orderBy('id', 'DESC')->status(['wait', 'process_payment'])->first();

                $limit                  = StoreSetting::type('limit_unique_number')->ondate('now')->first();

                if($prev_number['unique_number'] < $limit['value'])
                {
                    $unique_number      = $i+ $prev_number['unique_number'] + 1;
                }
                else
                {
                    $unique_number      = $i+ 1;
                }

                $amount                 = Sale::amount($sale->amount - $unique_number)->status(['wait', 'process_payment'])->notid($sale->id)->first();
                $i                      = $i+1;
            }

            return $unique_number;
        }
        else
        {
        	return $sale['unique_number'];
        }
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope to find bills
	 *
	 * @param string of bill
	 */
	public function scopeBills($query, $variable)
	{
		return 	$query->havingraw("
						sum(IFNULL((SELECT sum((price - discount) * quantity) FROM transaction_details WHERE transaction_details.transaction_id = transactions.id and transaction_details.deleted_at is null),0)
						+ IFNULL((SELECT sum(amount) FROM point_logs WHERE point_logs.reference_id = transactions.id and point_logs.deleted_at is null and point_logs.reference_type like '%Sale%' and point_logs.amount < 0),0)
						- IFNULL((SELECT sum(amount) FROM payments WHERE payments.transaction_id = transactions.id and payments.deleted_at is null),0)
						+ transactions.shipping_cost - transactions.voucher_discount - transactions.unique_number
						) = ".$variable);
	}
}
