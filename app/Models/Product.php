<?php

namespace App\Models;

use App\Models\Traits\HasCurrentPriceTrait;
use App\Models\Traits\HasCurrentStockTrait;
use App\Models\Traits\HasDefaultImageTrait;

use App\Models\Traits\HasSlugTrait;
use App\Models\Traits\HasNameTrait;
use App\Models\Traits\HasSelectAllTrait;
use \App\Models\Traits\HasStatableTrait;

use App\Models\Traits\HasPriceTrait;
use App\Models\Traits\HasStockTrait;
use App\Models\Traits\HasSellableTrait;
use App\Models\Traits\HasTransactionStatusTrait;
use App\Models\Observers\ProductObserver;

/**
 * Used for Product Models
 * 
 * @author cmooy
 */
class Product extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\hasMany\HasVariansTrait;
	use \App\Models\Traits\hasMany\HasPricesTrait;
	use \App\Models\Traits\hasMany\HasLabelsTrait;

	use \App\Models\Traits\belongsToMany\HasClustersTrait;

	use \App\Models\Traits\morphMany\HasImagesTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasSelectAllTrait;
	use HasCurrentStockTrait;
	use HasCurrentPriceTrait;
	use HasDefaultImageTrait;

	/**
	 * Global traits used as scope (plugged scope).
	 *
	 */
	use HasStockTrait;
	use HasSellableTrait;
	use HasPriceTrait;
	use HasTransactionStatusTrait;
	use HasSlugTrait;
	use HasNameTrait;
	use HasStatableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'products';

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
											'name'							,
											'upc'							,
											'slug'							,
											'description'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'name'							=> 'max:255',
											'upc'							=> 'max:255',
											'slug'							=> 'max:255',
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
 
        Product::observe(new ProductObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope to find upc of product
	 *
	 * @param string of upc
	 */
	public function scopeUPC($query, $variable)
	{
		return 	$query->where('upc', $variable);
	}

	/**
	 * scope to find slug of product
	 *
	 * @param string of slug
	 */
	public function scopeSlug($query, $variable)
	{
		return 	$query->where('slug', $variable);
	}
}
