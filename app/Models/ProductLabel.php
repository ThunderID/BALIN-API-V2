<?php

namespace App\Models;

// use App\Models\Observers\ProductLabelObserver;

/**
 * Used for ProductLabel Models
 * 
 * @author cmooy
 */
class ProductLabel extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasProductTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'product_lables';

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
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'started_at', 'ended_at'];

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
											'lable'							,
											'value'							,
											'started_at'					,
											'ended_at'						,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'product_id'					=> 'exists:products,id',
											'lable' 						=> 'max:255',
											'started_at'					=> 'date_format:"Y-m-d H:i:s"',
											// 'ended_at'						=> 'date_format:"Y-m-d H:i:s"',
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/

	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	/**
	 * boot
	 *
	 */
	public static function boot() 
	{
        parent::boot();
 
        // ProductLabel::observe(new ProductLabelObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/


	/**
	 * scope to find label of product
	 *
	 * @param string of labelnames
	 */
	public function scopeName($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn($query->getModel()->table.'.lable', $variable);
		}

		return 	$query->where($query->getModel()->table.'.lable', 'like', '%'.$variable.'%');
	}
}
