<?php

namespace App\Entities;

use App\Entities\Traits\HasStockTrait;

use App\Entities\Traits\GetAllTrait;

use App\Entities\TraitLibraries\SelectStockTrait;
use App\Entities\TraitLibraries\JoinProductTrait;
use App\Entities\TraitLibraries\JoinTransactionTrait;

use App\Entities\TraitRelations\BelongsToProductTrait;

use App\CrossServices\ClosedDoorModelObserver;

/**
 * Used for Varian Models
 * 
 * @author cmooy
 */
class Varian extends BaseModel
{
	/**
	 * Traits To Calculated Relations
	 */
	use HasStockTrait;
	
	/**
	 * Traits To Condition within itself
	 */
	use GetAllTrait;

	/**
	 * Libraries Traits for scopes
	 *
	 */
	use SelectStockTrait;
	use JoinProductTrait;
	use JoinTransactionTrait;
	
	/**
	 * Relationship Traits
	 *
	 */
	use BelongsToProductTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'varians';

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
 
        Varian::observe(new ClosedDoorModelObserver());
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
}
