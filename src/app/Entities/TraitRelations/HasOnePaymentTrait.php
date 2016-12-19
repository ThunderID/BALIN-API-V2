<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models has one payment.
 *
 * @author cmooy
 */
trait HasOnePaymentTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasOnePaymentTraitConstructor()
	{
		//
	}

	/**
	 * call has one relationship
	 *
	 **/
	public function Payment()
	{
		return $this->hasOne('App\Entities\Payment', 'transaction_id');
	}
}