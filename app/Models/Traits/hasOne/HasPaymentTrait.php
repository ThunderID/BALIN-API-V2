<?php namespace App\Models\Traits\hasOne;

/**
 * Trait for models has one payment.
 *
 * @author cmooy
 */
trait HasPaymentTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPaymentTraitConstructor()
	{
		//
	}

	/**
	 * call has one relationship
	 *
	 **/
	public function Payment()
	{
		return $this->hasOne('App\Models\Payment', 'transaction_id');
	}
}