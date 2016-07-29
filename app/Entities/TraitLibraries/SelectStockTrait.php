<?php 

namespace App\Entities\TraitLibraries;

/**
 * available function to get result of stock
 *
 * @author cmooy
 */
trait SelectStockTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function SelectStockTraitConstructor()
	{
		//
	}

	/**
	 * count total items in cart of customers' order
	 *
	 * @return cart_item
	 **/
	public function scopeSelectStockInCart($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="cart", quantity, 0), 0)
									),0) as cart_item')
		;
	}
	
	/**
	 * count current stock, from sale wait. paid, packed, shipping, delivered as out, and delivered purchase as in 
	 *
	 * @return current_stock
	 **/
	public function scopeSelectCurrentStock($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="wait" OR transaction_logs.status ="veritrans_processing_payment" OR transaction_logs.status ="paid" OR transaction_logs.status ="shipping" OR transaction_logs.status="packed" OR transaction_logs.status ="delivered", 0-quantity, 0), quantity)
									),0) as current_stock')
		;
	}
	
	/**
	 * count on hold stock, defined as checked out stock (sale wait)
	 *
	 * @return on_hold_stock
	 **/
	public function scopeSelectOnHoldStock($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="wait" OR transaction_logs.status ="veritrans_processing_payment", quantity, 0), 0)
									),0) as on_hold_stock')
		;
	}

	/**
	 * count packed stock (sale packed)
	 *
	 * @return packed_stock
	 **/
	public function scopeSelectPackedStock($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="packed", quantity, 0), 0)
									),0) as packed_stock')
		;
	}

	/**
	 * count inventory stock, defined as current stock in warehouse and belongs to warehouse (sale shipping, sale packed, sale delivered as out and purchase delivered as in)
	 *
	 * @return inventory_stock
	 **/
	public function scopeSelectInventoryStock($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="shipping" OR transaction_logs.status="packed" OR transaction_logs.status ="delivered", 0-quantity, 0), quantity)
									),0) as inventory_stock')
		;
	}

	/**
	 * count reserved stock, defined as stock exists in warehouse but does not belongs to shop (sale paid)
	 *
	 * @return packed_stock
	 **/
	public function scopeSelectReservedStock($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="paid", quantity, 0), 0)
									),0) as reserved_stock')
		;
	}

	/**
	 * count reserved stock, defined as stock exists in warehouse but does not belongs to shop (sale paid)
	 *
	 * @return packed_stock
	 **/
	public function scopeSelectSoldItem($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="paid" OR transaction_logs.status="packed" OR transaction_logs.status="shipping" OR transaction_logs.status="delivered", quantity, 0), 0)
									),0) as sold_item')
		;
	}
	
	/**
	 * combine all stocks' scope calculation
	 *
	 * @return current_stock, on_hold_stock, inventory_stock, reserved_stock, packed_stock, cart item, sold item
	 **/
	public function scopeSelectGlobalStock($query, $variable)
	{
		return $query->selectcurrentstock(true)->selectonholdstock(true)->selectinventorystock(true)->selectreservedstock(true)->selectpackedstock(true)->selectsolditem(true)->selectstockincart(true);
		;
	}

	/**
	 * condition of current_stock having certain value
	 *
	 * @param threshold (negative defined as less than, positive defined as greater than)
	 **/
	public function scopeHavingCurrentStock($query, $variable)
	{
		if($variable < 0)
		{
			$param 					= '<=';
			$variable 				= abs($variable);
		}
		else
		{
			$param 					= '>';
			$variable 				= abs($variable);
		}

		return $query->havingraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="wait" OR transaction_logs.status ="veritrans_processing_payment" OR transaction_logs.status ="paid" OR transaction_logs.status ="shipping" OR transaction_logs.status="packed" OR transaction_logs.status ="delivered", 0-quantity, 0), quantity)
									),0) '.$param.' '.$variable)
		;
	}

	/**
	 * condition of sold_item having certain value
	 *
	 * @param threshold (negative defined as less than, positive defined as greater than)
	 **/
	public function scopeHavingSoldItem($query, $variable)
	{
		if($variable < 0)
		{
			$param 					= '<=';
			$variable 				= abs($variable);
		}
		else
		{
			$param 					= '>';
			$variable 				= abs($variable);
		}

		return $query->havingraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="paid" OR transaction_logs.status="packed" OR transaction_logs.status="shipping" OR transaction_logs.status="delivered", quantity, 0), 0)
									),0) '.$param.' '.$variable)
		;
	}
}