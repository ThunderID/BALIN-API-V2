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
	
	/**
	 * joining varian from transaction detail
	 *
	 **/
	public function scopeJoinVarianFromTransactionDetail($query, $variable)
	{
		return $query
		->join('varians', function ($join) use($variable) 
			 {
									$join->on ( 'varians.id', '=', 'transaction_details.varian_id' )
									->wherenull('varians.deleted_at')
									;
			})
		;
	}

	/**
	 * joining product from varian
	 *
	 **/
	public function scopeJoinProductFromVarian($query, $variable)
	{
		return $query
		 ->join('products', function ($join) use($variable) 
			 {
									$join->on ( 'varians.product_id', '=', 'products.id' )
									->wherenull('products.deleted_at')
									;
			})
		;
	}
}