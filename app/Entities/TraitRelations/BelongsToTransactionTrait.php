<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for Entities belongs to Transaction.
 *
 * @author cmooy
 */
trait BelongsToTransactionTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function BelongsToTransactionTraitConstructor()
	{
		//
	}
	
	/**
	 * call belongsto relationship with transaction
	 *
	 **/
	public function Transaction()
	{
		return $this->belongsTo('App\Entities\Transaction');
	}
	
	/**
	 * check if model has transaction
	 *
	 **/
	public function scopeHasTransaction($query, $variable)
	{
		return $query->whereHas('transaction', function($q)use($variable){$q;});
	}

	/**
	 * check if model has transaction in certain id
	 *
	 * @var singular id
	 **/
	public function scopeTransactionID($query, $variable)
	{
		return $query->where('transaction_id', $variable);
	}

	/**
	 * check if model has transaction in certain name
	 *
	 * @var singular name
	 **/
	public function scopeTransactionName($query, $variable)
	{
		return $query->whereHas('transaction', function($q)use($variable){$q->name($variable);});
	}

	/**
	 * check if model has transaction in certain userid
	 *
	 * @var singular userid
	 **/
	public function scopeTransactionCustomerId($query, $variable)
	{
		return $query->whereHas('sale', function($q)use($variable){$q->userid($variable);});
	}

	/**
	 * call belongsto relationship with sale transaction
	 *
	 **/
	public function Sale()
	{
		return $this->belongsTo('App\Entities\Sale', 'transaction_id');
	}
}