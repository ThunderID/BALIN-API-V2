<?php 

namespace App\Entities\TraitLibraries;

use DB;

/**
 * available function who hath relationship with transactions' status
 *
 * @author cmooy
 */
trait JoinProductTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function JoinProductTraitConstructor()
	{
		//
	}

	/**
	 * left joining varian from product
	 *
	 **/
	public function scopeLeftJoinVarianFromProduct($query, $variable)
	{
		return $query
		 ->leftjoin('varians', function ($join) use($variable) 
		 {
			$join->on ( 'varians.product_id', '=', 'products.id' )
			->wherenull('varians.deleted_at')
			;
		});
	}
	
	/**
	 * left joining transaction detail from varian
	 *
	 **/
	public function scopeLeftJoinTransactionDetailFromVarian($query, $variable)
	{
		return $query
		 ->leftjoin('transaction_details', function ($join) use($variable) 
		 {
			$join->on ( 'transaction_details.varian_id', '=', 'varians.id' )
			->wherenull('transaction_details.deleted_at')
			;
		})
		;
	}

}