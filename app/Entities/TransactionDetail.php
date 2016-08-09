<?php

namespace App\Entities;

use App\CrossServices\ClosedDoorModelObserver;

use App\Entities\TraitLibraries\JoinTransactionTrait;

use App\Entities\TraitRelations\BelongsToVarianTrait;
use App\Entities\TraitRelations\BelongsToTransactionTrait;

class TransactionDetail extends BaseModel
{
	/**
	 * Libraries Traits for scopes
	 *
	 */
	use JoinTransactionTrait;

	/**
	 * Relationship Traits
	 *
	 */
	use BelongsToVarianTrait;
	use BelongsToTransactionTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'transaction_details';
	
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
		
	public static function boot() 
	{
        parent::boot();
 
        TransactionDetail::observe(new ClosedDoorModelObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

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
