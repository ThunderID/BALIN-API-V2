<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

use App\Models\Traits\HasStockTrait;
use App\Models\Traits\HasTransactionStatusTrait;
// use App\Models\Observers\TransactionDetailObserver;

/**
 * Used for TransactionDetail Models
 * 
 * @author cmooy
 */
class TransactionDetail extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasVarianTrait;
	use \App\Models\Traits\belongsTo\HasTransactionTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasStockTrait;
	use HasTransactionStatusTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'transaction_details';

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
											'transaction_id'				,
											'varian_id'						,
											'quantity'						,
											'price'							,
											'discount'						,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'transaction_id'				=> 'exists:transactions,id',
											'varian_id'						=> 'exists:varians,id',
											'quantity'						=> 'numeric',
											'price'							=> 'numeric',
											'discount'						=> 'numeric',
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
 
        // TransactionDetail::observe(new TransactionDetailObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope to get selled product
	 *
	 * @return stock in, stock out, varian_id, transact_at
	 */	
	public function scopeSoldItem($query, $variable)
	{
		return 	$query
					->selectraw('transaction_details.varian_id')
					->selectraw('sum(if(transactions.type = "sell", quantity, 0)) as sold_item')
					->TransactionStockOn(['paid', 'packed', 'shipping', 'delivered'])
					->groupby('transaction_details.varian_id')
					;
		;
	}

	/**
	 * scope to check stock movement of varian on certain time
	 *
	 * @return stock in, stock out, varian_id, transact_at
	 */	
	public function scopeStockMovement($query, $variable)
	{
		return 	$query
					->selectraw('transaction_details.varian_id')
					->selectraw('transactions.ref_number as ref')
					->selectraw('transactions.transact_at')
					->selectraw('sum(if(transactions.type = "buy", quantity, 0)) as stock_in')
					->selectraw('sum(if(transactions.type = "sell", quantity, 0)) as stock_out')
					->TransactionStockOn(['paid', 'packed', 'shipping', 'delivered'])
					->groupby('transactions.id')
					->orderByRaw(DB::raw('varian_id asc, transactions.transact_at asc'))
					;
		;
	}

	/**
	 * scope to check critical stock that below margin (current_stock)
	 *
	 * @param treshold
	 */	
	public function scopeCritical($query, $variable)
	{
		return 	$query
				->selectraw('transaction_details.*')
				// ->selectcurrentstock(true)
				->TransactionStockOn(['wait', 'veritrans_processing_payment', 'paid', 'packed', 'shipping', 'delivered'])
				->HavingCurrentStock($variable)
				// ->orderby('current_stock', 'asc')
				->groupBy('varian_id')
				;
	}
}
