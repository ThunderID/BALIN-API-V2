<?php

namespace App\Entities\TraitRelations;

/**
 * Trait for models has many TransactionLogs.
 *
 * @author cmooy
 */
trait HasManyTransactionLogsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasManyTransactionLogsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function TransactionLogs()
	{
		return $this->hasMany('App\Entities\TransactionLog', 'transaction_id');
	}

	/**
	 * call has many relationship in orderlogs where status in wait, paid, packed, shipping, delivered
	 *
	 **/
	public function OrderLogs()
	{
		return $this->hasMany('App\Entities\TransactionLog', 'transaction_id')->wherein('status', ['wait', 'payment_process', 'paid', 'packed', 'shipping', 'delivered', 'canceled']);
	}

	/**
	 * check if model has transaction logs
	 *
	 **/
	public function scopeHasTransactionLogs($query, $variable)
	{
		return $query->whereHas('transactionlogs', function($q)use($variable){$q;});
	}

	/**
	 * check if model has transaction logs in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeTransactionLogID($query, $variable)
	{
		return $query->whereHas('transactionlogs', function($q)use($variable){$q->id($variable);});
	}

	/**
	 * find status in transaction logs statuses
	 *
	 * @var string of status
	 **/
	public function scopeStatus($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn('status', $variable);
		}

		return 	$query->where('status', $variable);
	}
}