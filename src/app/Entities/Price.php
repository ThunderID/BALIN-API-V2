<?php

namespace App\Entities;

// use App\CrossServices\ClosedDoorModelObserver;

use App\Entities\TraitRelations\BelongsToProductTrait;

class Price extends BaseModel
{
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
	protected $table				= 'prices';
	
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
	protected $appends				=	['discount'];

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
											'price'							,
											'promo_price'					,
											'started_at'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'product_id'					=> 'exists:products,id',
											'price'							=> 'numeric',
											'promo_price'					=> 'numeric|max:price',
											'started_at'					=> 'date_format:"Y-m-d H:i:s"',
										];

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/**
     * Get the discount.
     *
     * @param  string  $value
     * @return string
     */
    public function getDiscountAttribute($value)
    {
    	if($value['promo_price'] == 0)
    	{
    		$discount 				= 0;
    	}
    	else
    	{
    		$discount 				= $value['price'] - $value['promo_price'];
    	}

        return $discount;
    }

	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        // Price::observe(new ClosedDoorModelObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	public function scopeOnDate($query, $variable)
	{
		if(is_array($variable))
		{
			$started_at 	= date('Y-m-d H:i:s', strtotime($variable[0]));
			$ended_at 		= date('Y-m-d H:i:s', strtotime($variable[1]));

			return $query->where('started_at', '>=', $started_at)
						->where('started_at', '<=', $ended_at);
		}
		else
		{
			$ondate 	= date('Y-m-d H:i:s', strtotime($variable));

			return $query->where('started_at', '<=', $ondate);
		}
	}
}
