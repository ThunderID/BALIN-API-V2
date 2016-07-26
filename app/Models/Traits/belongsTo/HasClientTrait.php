<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs to user.
 *
 * @author cmooy
 */
trait HasClientTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasClientTraitConstructor()
	{
		//
	}

	/**
	 * check if model has client in certain id
	 *
	 * @var string id
	 **/
	public function scopeClientID($query, $variable)
	{
		return $query->where('client_id', $variable);
	}
}