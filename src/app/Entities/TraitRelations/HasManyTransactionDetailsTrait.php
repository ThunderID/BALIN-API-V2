<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models has many TransactionLogs.
 *
 * @author cmooy
 */
trait HasManyTransactionDetailsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasManyTransactionDetailsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function TransactionDetails()
	{
		return $this->hasMany('App\Entities\TransactionDetail', 'transaction_id');
	}

	/**
	 * check if model has transaction details
	 *
	 **/
	public function scopeHasTransactionDetails($query, $variable)
	{
		return $query->whereHas('transactiondetails', function($q)use($variable){$q;});
	}
	
	/**
	 * check if model has transaction details in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeTransactionDetailID($query, $variable)
	{
		return $query->whereHas('transactiondetails', function($q)use($variable){$q->id($variable);});
	}
}