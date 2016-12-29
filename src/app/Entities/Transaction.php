<?php

namespace App\Entities;

use App\CrossServices\ClosedDoorModelObserver;

/**
 * Used for Sale and Purchase Models
 * 
 * @author cmooy
 */
class Transaction extends BaseModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'transactions';


	/**
	 * Date will be returned as carbon
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'transact_at'];

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

        Transaction::observe(new ClosedDoorModelObserver());
	}

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope search based on not issuer (pk)
	 *
	 * @param string or array of issuer
	 */	
	public function scopeNotIssuer($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereNotIn($query->getModel()->table.'.issuer', $variable);
		}

		if(is_null($variable))
		{
			return $query;
		}

		return 	$query->where($query->getModel()->table.'.issuer', '<>', $variable);
	}
}
