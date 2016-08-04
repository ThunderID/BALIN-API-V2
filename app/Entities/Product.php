<?php

namespace App\Entities;

use App\Entities\Traits\HasStockTrait;
use App\Entities\Traits\HasPriceTrait;
use App\Entities\Traits\HasImageTrait;

use App\Entities\Traits\GetAllTrait;

use App\Entities\TraitLibraries\SelectStockTrait;
use App\Entities\TraitLibraries\JoinProductTrait;
use App\Entities\TraitLibraries\JoinTransactionTrait;
use App\Entities\TraitLibraries\FieldNameTrait;
use App\Entities\TraitLibraries\FieldSlugTrait;
use App\Entities\TraitLibraries\SellableTrait;
use App\Entities\TraitLibraries\SelectPriceTrait;
use App\Entities\TraitLibraries\SelectStatTrait;

use App\Entities\TraitRelations\HasManyVariansTrait;
use App\Entities\TraitRelations\HasManyPricesTrait;
use App\Entities\TraitRelations\HasManyProductLabelsTrait;
use App\Entities\TraitRelations\MorphManyImagesTrait;
use App\Entities\TraitRelations\BelongsToManyClustersTrait;

use App\CrossServices\ClosedDoorModelObserver;

/**
 * Used for Product Models
 * 
 * @author cmooy
 */
class Product extends BaseModel
{
	/**
	 * Traits To Calculated Relations
	 */
	use HasStockTrait;
	use HasPriceTrait;
	use HasImageTrait;
	
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
	use FieldNameTrait;
	use FieldSlugTrait;
	use SellableTrait;
	use SelectPriceTrait;
	use SelectStatTrait;

	/**
	 * Relationship Traits
	 *
	 */
	use HasManyVariansTrait;
	use HasManyPricesTrait;
	use HasManyProductLabelsTrait;
	use MorphManyImagesTrait;
	use BelongsToManyClustersTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'products';

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
 
        Product::observe(new ClosedDoorModelObserver());
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
}
