<?php namespace App\Models\Traits\hasMany;

use DB;

/**
 * Trait for models has one shipment.
 *
 * @author cmooy
 */
trait HasShipmentsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasShipmentsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Shipments()
	{
		return $this->hasMany('App\Models\Shipment');
	}


	/**
	 * call has many relationship
	 *
	 **/
	public function Shippings()
	{
		return $this->hasMany('App\Models\Shipment')->join('transactions', function ($join)
			 {
									$join->on ( 'shipments.transaction_id', '=', 'transactions.id' )
									->wherenull('transactions.deleted_at')
									;
			})
			->join('transaction_logs', function ($join)
			 {
                                    $join->on ( 'transaction_logs.transaction_id', '=', 'transactions.id' )
									->on(DB::raw('(transaction_logs.id = (select id from transaction_logs as tl2 where tl2.transaction_id = transaction_logs.transaction_id and tl2.deleted_at is null order by tl2.changed_at desc limit 1))'), DB::raw(''), DB::raw(''))
                                    ->where('transaction_logs.status', '=', 'shipping')
                                    ->wherenull('transaction_logs.deleted_at')
                                    ;
			})
			;
	}
}
