<?php namespace App\Models\Traits;

use DB;

/**
 * available function who hath relationship with transactions' status
 *
 * @author cmooy
 */
trait HasTransactionStatusTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasTransactionStatusTraitConstructor()
	{
		//
	}

	/**
	 * left joining transaction from supplier
	 *
	 **/
	public function scopeLeftJoinTransactionFromSupplier($query, $variable)
	{
		return $query
		 ->join('transactions', function ($join) use($variable) 
			 {
									$join->on ( 'suppliers.id', '=', 'transactions.supplier_id' )
									->wherenull('transactions.deleted_at')
									;
			})
		;
	}

	/**
	 * joining transaction from shipment
	 *
	 **/
	public function scopeJoinTransactionFromShipment($query, $variable)
	{
		return $query
		 ->join('transactions', function ($join) use($variable) 
			 {
									$join->on ( 'shipments.transaction_id', '=', 'transactions.id' )
									->wherenull('transactions.deleted_at')
									;
			})
		;
	}

	/**
	 * joining transaction from transaction detail
	 *
	 **/
	public function scopeJoinTransaction($query, $variable)
	{
		return $query
		 ->join('transactions', function ($join) use($variable) 
			 {
									$join->on ( 'transaction_details.transaction_id', '=', 'transactions.id' )
									->wherenull('transactions.deleted_at')
									;
			})
		;
	}

	/**
	 * left joining transaction from transaction detail
	 *
	 **/
	public function scopeLeftJoinTransaction($query, $variable)
	{
		return $query
		 ->leftjoin('transactions', function ($join) use($variable) 
			 {
									$join->on ( 'transaction_details.transaction_id', '=', 'transactions.id' )
									->wherenull('transactions.deleted_at')
									
									;
			})
		;
	}

	/**
	 * joining transaction logs from transaction for transaction log
	 *
	 * @param string or array of status
	 **/
	public function scopeTransactionLogStatus($query, $variable)
	{

		if(!is_array($variable))
		{
			return $query
			 ->join('transaction_logs', function ($join) use($variable) 
			 {
									$join->on ( 'transaction_logs.transaction_id', '=', 'transactions.id' )
									->on(DB::raw('(transaction_logs.id = (select id from transaction_logs as tl2 where tl2.transaction_id = transaction_logs.transaction_id and tl2.deleted_at is null order by tl2.changed_at desc limit 1))'), DB::raw(''), DB::raw(''))
									->where('transaction_logs.status', '=', $variable)
									->wherenull('transaction_logs.deleted_at')
									;
			})
			;
		}
		else
		{
			return $query
			 ->join('transaction_logs', function ($join) use($variable) 
			 {
									$join->on ( 'transaction_logs.transaction_id', '=', 'transactions.id' )
									->on(DB::raw('(transaction_logs.id = (select id from transaction_logs as tl2 where tl2.transaction_id = transaction_logs.transaction_id and tl2.deleted_at is null order by tl2.changed_at desc limit 1))'), DB::raw(''), DB::raw(''))
									->whereIn('transaction_logs.status', $variable)
									->wherenull('transaction_logs.deleted_at')
									;
			})
			;
		}
	}
	
	/**
	 * left joining transaction logs from transaction for transaction log
	 *
	 * @param string or array of status
	 **/
	public function scopeLeftTransactionLogStatus($query, $variable)
	{

		if(!is_array($variable))
		{
			return $query
			 ->leftjoin('transaction_logs', function ($join) use($variable) 
			 {
									$join->on ( 'transaction_logs.transaction_id', '=', 'transactions.id' )
									->on(DB::raw('(transaction_logs.id = (select id from transaction_logs as tl2 where tl2.transaction_id = transaction_logs.transaction_id and tl2.deleted_at is null order by tl2.changed_at desc limit 1))'), DB::raw(''), DB::raw(''))
									->where('transaction_logs.status', '=', $variable)
									->wherenull('transaction_logs.deleted_at')
									;
			})
			;
		}
		else
		{
			return $query
			 ->leftjoin('transaction_logs', function ($join) use($variable) 
			 {
									$join->on ( 'transaction_logs.transaction_id', '=', 'transactions.id' )
									->on(DB::raw('(transaction_logs.id = (select id from transaction_logs as tl2 where tl2.transaction_id = transaction_logs.transaction_id and tl2.deleted_at is null order by tl2.changed_at desc limit 1))'), DB::raw(''), DB::raw(''))
									->whereIn('transaction_logs.status', $variable)
									->wherenull('transaction_logs.deleted_at')
									;
			})
			;
		}
	}
	
	/**
	 * check transaction log changed date
	 *
	 * @param string or array of datetime changed_at
	 **/
	public function scopeTransactionLogChangedAt($query, $variable)
	{
		if(!is_array($variable))
		{
			return $query->where('changed_at', '<=', date('Y-m-d H:i:s', strtotime($variable)))->orderBy('changed_at', 'desc');
		}
		else
		{
			if(is_null($variable[0]))
			{
				return $query->where('changed_at', '<=', date('Y-m-d H:i:s', strtotime($variable[1])));
			}
		}

		return $query->where('changed_at', '>=', date('Y-m-d H:i:s', strtotime($variable[0])))->where('changed_at', '<=', date('Y-m-d H:i:s', strtotime($variable[1])))->orderBy('changed_at', 'asc');
	}
	

	/**
	 * check transaction transact date
	 *
	 * @param string or array of datetime transact_at
	 **/
	public function scopeTransactionTransactAt($query, $variable)
	{
		if(!is_array($variable))
		{
			return $query->where('transact_at', '<=', date('Y-m-d H:i:s', strtotime($variable)))->orderBy('transact_at', 'desc');
		}

		return $query->where('transact_at', '>=', date('Y-m-d H:i:s', strtotime($variable[0])))->where('transact_at', '<=', date('Y-m-d H:i:s', strtotime($variable[1])))->orderBy('changed_at', 'asc');
	}

	/**
	 * check transaction type
	 *
	 * @param string or array of type
	 **/
	public function scopeExtendTransactionType($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn('transactions.type', $variable)
			;
		}
		else
		{
			return $query->where('transactions.type', $variable)
			;
		}
	}

	/**
	 * collaborate join transaction, and logs for purchase and sale transaction
	 *
	 * @param string or array of transaction current status
	 **/
	public function scopeTransactionStockOn($query, $variable)
	{
		return $query->jointransaction(true)->transactionlogstatus($variable)->extendtransactiontype(['sell', 'buy'])
		;
	}

	/**
	 * collaborate left join transaction, and logs for purchase and sale transaction
	 *
	 * @param string or array of transaction current status
	 **/
	public function scopeLeftTransactionStockOn($query, $variable)
	{
		return $query->leftjointransaction(true)->lefttransactionlogstatus($variable)
		;
	}

	/**
	 * collaborate join transaction, and logs for sale transaction
	 *
	 * @param string or array of transaction current status
	 **/
	public function scopeTransactionSellOn($query, $variable)
	{
		return $query->jointransaction(true)->transactionlogstatus($variable)->extendtransactiontype('sell')
		;
	}

	/**
	 * collaborate left join transaction, and logs for purchase and purchase transaction
	 *
	 * @param string or array of transaction current status
	 **/
	public function scopeTransactionBuyOn($query, $variable)
	{
		return $query->jointransaction(true)->transactionlogstatus($variable)->extendtransactiontype('buy')
		;
	}

	/**
	 * joining varian from product
	 *
	 **/
	public function scopeJoinVarianFromProduct($query, $variable)
	{
		return $query
		 ->join('varians', function ($join) use($variable) 
		 {
			$join->on ( 'varians.product_id', '=', 'products.id' )
			->wherenull('varians.deleted_at')
			;
		})
		;
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
		})
		;
	}

	/**
	 * joining transaction detail from product
	 *
	 **/
	public function scopeJoinTransactionDetailFromProduct($query, $variable)
	{
		return $query
		->join('varians', function ($join) use($variable) 
			 {
									$join->on ( 'varians.product_id', '=', 'products.id' )
									->wherenull('varians.deleted_at')
									;
			})
		->join('transaction_details', function ($join) use($variable) 
			 {
									$join->on ( 'transaction_details.varian_id', '=', 'varians.id' )
									->wherenull('transaction_details.deleted_at')
									;
			})
		;
	}

	/**
	 * joining transaction detail from varian
	 *
	 **/
	public function scopeJoinTransactionDetailFromVarian($query, $variable)
	{
		return $query
		->join('transaction_details', function ($join) use($variable) 
			 {
									$join->on ( 'transaction_details.varian_id', '=', 'varians.id' )
									->wherenull('transaction_details.deleted_at')
									;
			})
		;
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
	 * right joining category from categories_products ?
	 *
	 * @param string or array of category id
	 **/
	public function scopeCategoryAncestorSuccessor($query, $variable)
	{
		if(!is_array($variable))
		{
			return $query
			 ->rightjoin('categories', function ($join) use($variable) 
			 {
									$join->on( 'categories.id', '=', 'categories_products.category_id' )
									// ->oron('categories.id', '=', 'categories_products.category_id')
									// ->where('categories.category_id', '=', $variable)
									->where('categories.id', '=', $variable)
									->wherenull('categories.deleted_at')
									;
			})
			;
		}
		else
		{
			return $query
			 ->rightjoin('categories', function ($join) use($variable) 
			 {
									$join->on ( 'categories.id', '=', 'categories_products.category_id' )
									->oron('categories.id', 'categories_products.category_id')
									->where('categories.category_id', '=', $variable)
									->orwhere('categories.id', '=', $variable)
									->wherenull('categories.deleted_at')
									;
			})
			;
		}
	}

	/**
	 * joining transaction detail from transaction
	 *
	 **/
	public function scopeJoinTransactionDetailFromTransaction($query, $variable)
	{
		return $query
		 ->join('transaction_details', function ($join) use($variable) 
		 {
			$join->on ( 'transaction_details.transaction_id', '=', 'transactions.id' )
			->wherenull('transaction_details.deleted_at')
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

	/**
	 * joining shipment from transaction
	 *
	 **/
	public function scopeJoinShipmentFromTransaction($query, $variable)
	{
		return $query
		->join('shipments', function ($join) use($variable) 
		 {
			$join->on ( 'shipments.transaction_id', '=', 'transactions.id' )
			->wherenull('shipments.deleted_at')
			;
		})
		;
	}

	/**
	 * joining shipment from transaction
	 *
	 **/
	public function scopeJoinAddressFromShipment($query, $variable)
	{
		return $query
		->join('addresses', function ($join) use($variable) 
		 {
			$join->on ( 'addresses.id', '=', 'shipments.address_id' )
			->wherenull('addresses.deleted_at')
			;
		})
		;
	}
}