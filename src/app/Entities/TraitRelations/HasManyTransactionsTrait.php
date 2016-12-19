<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models has many TransactionLogs.
 *
 * @author cmooy
 */
trait HasManyTransactionsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasManyTransactionsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Transactions()
	{
		return $this->hasMany('App\Entities\Transaction');
	}

	/**
	 * check if model has transaction
	 *
	 **/
	public function scopeHasTransactions($query, $variable)
	{
		return $query->whereHas('transactions', function($q)use($variable){$q;});
	}

	/**
	 * check if model has transaction in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeTransactionID($query, $variable)
	{
		return $query->whereHas('transactions', function($q)use($variable){$q->id($variable);});
	}

	/**
	 * call has many in term of displaying orders 
	 *
	 **/
	public function MyOrders()
	{
		return $this->hasMany('App\Entities\Sale', 'user_id')->wherein('status', ['wait', 'veritrans_processing_payment', 'canceled', 'paid', 'shipping', 'packed', 'delivered']);
    }

	/**
	 * call has many in term of sale
	 *
	 **/
	public function Sales()
	{
		return $this->hasMany('App\Entities\Sale', 'user_id');
    }
}