<?php

namespace App\Models;

use App\Models\Traits\HasDefaultImageTrait;
use App\Models\Traits\HasNameTrait;
use App\Models\Traits\HasSelectAllTrait;

use App\Models\Observers\ProductExtensionObserver;

/**
 * Used for ProductExtension Models
 * 
 * @author cmooy
 */
class ProductExtension extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\morphMany\HasImagesTrait;
	use \App\Models\Traits\hasMany\HasTransactionExtensionsTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasSelectAllTrait;
	use HasDefaultImageTrait;

	/**
	 * Global traits used as query builder (plugged scope).
	 *
	 */
	use HasNameTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'product_extensions';

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
											'price'							,
											'is_active'						,
											'is_customize'					,
											'description'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'name'							=> 'required|max:255',
											'price'							=> 'numeric',
											'is_active'						=> 'boolean',
											'is_customize'					=> 'boolean',
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
 
        ProductExtension::observe(new ProductExtensionObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope active product extension
	 *
	 * @param string of active
	 */
	public function scopeActive($query, $variable)
	{
		return 	$query->where('is_active', true);
	}
}
