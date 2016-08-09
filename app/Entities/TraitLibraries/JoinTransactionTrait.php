<?php 

namespace App\Entities\TraitLibraries;

use DB;

/**
 * available function who hath relationship with transactions' status
 *
 * @author cmooy
 */
trait JoinTransactionTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function JoinTransactionTraitConstructor()
	{
		//
	}

	/**
	 * joining transaction from transaction detail
	 *
	 **/
	public function scopeJoinTransactionFromTransactionDetail($query, $variable)
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
	public function scopeLeftJoinTransactionFromTransactionDetail($query, $variable)
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
	 * joining transaction logs from transaction for transaction log
	 *
	 * @param string or array of status
	 **/
	public function scopeJoinTransactionLogFromTransactionOnStatus($query, $variable)
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
	public function scopeLeftJoinTransactionLogFromTransactionOnStatus($query, $variable)
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
	 * collaborate join transaction, and logs for sale transaction
	 *
	 * @param string or array of transaction current status
	 **/
	public function scopeTransactionSellOn($query, $variable)
	{
		return $query->JoinTransactionFromTransactionDetail(true)->JoinTransactionLogFromTransactionOnStatus($variable)->transactiontype('sell');
	}

	/**
	 * collaborate left join transaction, and logs for purchase and purchase transaction
	 *
	 * @param string or array of transaction current status
	 **/
	public function scopeTransactionBuyOn($query, $variable)
	{
		return $query->JoinTransactionFromTransactionDetail(true)->JoinTransactionLogFromTransactionOnStatus($variable)->transactiontype('buy');
	}
	
	/**
	 * check transaction type
	 *
	 * @param string or array of type
	 **/
	public function scopeTransactionType($query, $variable)
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
}