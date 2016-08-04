<?php 

namespace App\Entities\TraitLibraries;

/**
 * available function who hath relationship with transactions' status
 *
 * @author cmooy
 */
trait SelectProductNotesTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function SelectProductNotesTraitConstructor()
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