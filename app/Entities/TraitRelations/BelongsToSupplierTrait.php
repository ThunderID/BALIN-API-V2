<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models belongs to Supplier.
 *
 * @author cmooy
 */
trait BelongsToSupplierTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function BelongsToSupplierTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function Supplier()
	{
		return $this->belongsTo('App\Entities\Supplier');
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