<?php namespace App\Models\Traits\belongsToMany;

/**
 * Trait for models belongs to many Transactions.
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
	 * call belongsto many relationship for transactions
	 *
	 **/
	public function Transactions()
	{
		return $this->belongsToMany('App\Models\Transaction', 'transaction_details');
	}

	/**
	 * check if model has transaction in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeTransactionsID($query, $variable)
	{
		return $query->whereHas('transactions', function($q)use($variable){$q->id($variable);});
	}
}