<?php


namespace App\Entities;

use App\Entities\Traits\HasImageTrait;

use App\Entities\Traits\GetAllTrait;

use App\Entities\TraitRelations\MorphManyImagesTrait;

use App\CrossServices\ClosedDoorModelObserver;

/**
 * Used for ProductExtension Models
 * 
 * @author cmooy
 */
class ProductExtension extends BaseModel
{
	/**
	 * Traits To Calculated Relations
	 */
	use HasImageTrait;
	
	/**
	 * Traits To Condition within itself
	 */
	use GetAllTrait;

	/**
	 * Relationship Traits
	 *
	 */
	use MorphManyImagesTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'product_extensions';

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
 
        ProductExtension::observe(new ClosedDoorModelObserver());
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
