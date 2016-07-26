<?php namespace App\Models\Traits\hasOne;

/**
 * Trait for models has one Referral.
 *
 * @author cmooy
 */
trait HasReferralTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasReferralTraitConstructor()
	{
		//
	}

	/**
	 * call has one relationship
	 *
	 **/
	public function Referral()
	{
		return $this->hasOne('App\Models\Referral', 'user_id');
	}

	/**
	 * find referral_code
	 * 
	 * @param referral_code
	 */	
	public function scopeReferralCode($query, $variable)
	{
		return $query->wherehas('referral', function($q)use($variable){$q->code($variable);});
	}
}