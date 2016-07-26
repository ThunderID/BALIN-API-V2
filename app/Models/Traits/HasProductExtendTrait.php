<?php namespace App\Models\Traits;

use DB;

/**
 * available function who hath relationship with transactions' status
 *
 * @author cmooy
 */
trait HasProductExtendTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasProductExtendTraitConstructor()
	{
		//
	}

	/**
	 * left joining transaction from supplier
	 *
	 **/
	public function scopeProductNotes($query, $variable)
	{
		return $query->selectraw('GROUP_CONCAT(
										CONCAT_WS(
											CONCAT_WS(" ( " , CONCAT_WS(
																" size ", products.name, varians.size
																), transaction_details.quantity
												),
											" ", " pcs ) "
											)
										) as product_notes')->JoinTransactionDetailFromTransaction(true)->JoinVarianFromTransactionDetail(true)->JoinProductFromVarian(true);
	}
}