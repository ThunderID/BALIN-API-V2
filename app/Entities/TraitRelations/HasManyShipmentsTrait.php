<?php 

namespace App\Entities\TraitRelations;

use DB;

/**
 * Trait for models has many shipment.
 *
 * @author cmooy
 */
trait HasManyShipmentsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasManyShipmentsTraitConstructor()
	{
		//
	}


	/**
	 * call has many relationship
	 *
	 **/
	public function Shipments()
	{
		return $this->hasMany('App\Entities\Shipment');
	}


	/**
	 * call has many relationship
	 *
	 **/
	public function Shippings()
	{
		return $this->hasMany('App\Entities\Shipment')->join('transactions', function ($join)
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