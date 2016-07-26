<?php

namespace App\Models;

// use App\Models\Observers\ShippingCostObserver;

/**
 * Used for ShippingCost Models
 * 
 * @author cmooy
 */
class ShippingCost extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasCourierTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'shipping_costs';

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
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'started_at'];

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
											'courier_id'					,
											'start_postal_code'				,
											'end_postal_code'				,
											'started_at'					,
											'cost'							,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'courier_id'					=> 'exists:couriers,id',
											'start_postal_code'				=> 'max:255',
											'end_postal_code'				=> 'max:255',
											'cost'							=> 'numeric',
											'started_at'					=> 'date_format:"Y-m-d H:i:s"'/*|after:now*/,
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
 
        // ShippingCost::observe(new ShippingCostObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
	
	/**
	 * scope to find postal code in a range
	 *
	 * @param string of postal code
	 */
	public function scopePostalCode($query, $variable)
	{
		return 	$query->where(function($query) use($variable) 
							{
							$query->where('start_postal_code','<=', $variable)
								->where('end_postal_code','>=', $variable);
							})
						->where('started_at', '<=', date('Y-m-d H:i:s'))
						->orderby('started_at', 'desc');
	}
}
