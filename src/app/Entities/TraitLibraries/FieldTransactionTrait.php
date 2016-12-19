<?php 

namespace App\Entities\TraitLibraries;

/**
 * available function who hath name trait
 *
 * @author cmooy
 */
trait FieldTransactionTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function FieldTransactionTraitConstructor()
	{
		//
	}

	/**
	 * scope to get condition where name
	 *
	 * @param string or array of entity' name
	 **/
	public function scopeRefNumber($query, $variable)
	{
		return 	$query->where($query->getModel()->table.'.ref_number', $variable);
	}
}