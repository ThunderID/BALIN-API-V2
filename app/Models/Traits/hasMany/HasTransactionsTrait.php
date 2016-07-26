<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Transactions.
 *
 * @author cmooy
 */
trait HasTransactionsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasTransactionsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Transactions()
	{
		return $this->hasMany('App\Models\Transaction');
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
		return $this->hasMany('App\Models\Sale', 'user_id')->wherein('status', ['wait', 'veritrans_processing_payment', 'canceled', 'paid', 'shipping', 'packed', 'delivered']);
    }

	/**
	 * call has many in term of sale
	 *
	 **/
	public function Sales()
	{
		return $this->hasMany('App\Models\Sale', 'user_id');
    }
}