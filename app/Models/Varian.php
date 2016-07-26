<?php

namespace App\Models;

use App\Models\Traits\HasCurrentPriceTrait;
use App\Models\Traits\HasCurrentStockTrait;
use App\Models\Traits\HasDefaultImageTrait;
use App\Models\Traits\HasSelectAllTrait;

use App\Models\Traits\HasStockTrait;
use App\Models\Traits\HasTransactionStatusTrait;
use App\Models\Observers\VarianObserver;

use Illuminate\Support\Facades\DB;

/**
 * Used for Varian Models
 * 
 * @author cmooy
 */
class Varian extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasProductTrait;
	use \App\Models\Traits\belongsToMany\HasTransactionsTrait;
	
	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasSelectAllTrait;
	use HasCurrentStockTrait;

	/**
	 * Global traits used as scope (plugged scope).
	 *
	 */
	use HasStockTrait;
	use HasTransactionStatusTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'varians';

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
											'product_id'					,
											'size'							,
											'sku'							,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'product_id'					=> 'exists:products,id',
											'size'							=> 'max:255',
											'sku'							=> 'max:255',
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
 
        Varian::observe(new VarianObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
	
	/**
	 * scope to find sku of product varian
	 *
	 * @param string of sku
	 */
	public function scopeSKU($query, $variable)
	{
		return 	$query->where('sku', $variable);
	}
	
	/**
	 * scope to find size of product varian
	 *
	 * @param string of size
	 */
	public function scopeSize($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn('size', $variable);
		}

		return 	$query->where('size', $variable);
	}

	/**
	 * scope to find varian who hath current stock
	 *
	 * @param threshold
	 */
	public function scopeCritical($query, $variable)
	{
		return 	$query
				->HavingCurrentStock($variable)
				// ->orderby('current_stock', 'asc')
				;
	}
}
