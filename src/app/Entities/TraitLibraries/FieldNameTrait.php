<?php 

namespace App\Entities\TraitLibraries;

/**
 * available function who hath name trait
 *
 * @author cmooy
 */
trait FieldNameTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function FieldNameTraitConstructor()
	{
		//
	}

	/**
	 * scope to get condition where name
	 *
	 * @param string or array of entity' name
	 **/
	public function scopeName($query, $variable)
	{
		if(is_array($variable))
		{
			foreach ($variable as $key => $value) 
			{
				$query = $query->where($query->getModel()->table.'.name', 'like', '%'.$value.'%');
			}

			return $query;
		}
		return 	$query->where($query->getModel()->table.'.name', 'like', '%'.$variable.'%');
	}
}