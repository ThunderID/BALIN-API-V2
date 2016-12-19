<?php 

namespace App\Entities\TraitLibraries;

use DB;

/**
 * available function to get result of stock
 *
 * @author cmooy
 */
trait CustomTimeQueryTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function CustomTimeQueryTraitConstructor()
	{
		//
	}

	/**
	 * scope to find history of date
	 *
	 * @param string of history
	 */
	public  function scopeDefault($query, $variable = true)
	{
		return $query->whereraw(DB::raw('tmp_store_settings.id = (select id from tmp_store_settings as tl2 where tl2.type = tmp_store_settings.type and tl2.deleted_at is null order by tl2.started_at desc limit 1)')) ;
	}
}