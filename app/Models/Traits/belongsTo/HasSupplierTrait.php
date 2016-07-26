<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs to supplier.
 *
 * @author cmooy
 */
trait HasSupplierTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasSupplierTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Supplier()
	{
		return $this->belongsTo('App\Models\Supplier');
	}

	/**
	 * check if model has supplier
	 *
	 **/
	public function scopeHasSupplier($query, $variable)
	{
		return $query->whereHas('supplier', function($q)use($variable){$q;});
	}

	/**
	 * check if model has supplier in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeSupplierID($query, $variable)
	{
		return $query->whereHas('supplier', function($q)use($variable){$q->id($variable);});
	}
}