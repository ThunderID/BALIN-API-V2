<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models has one Referral.
 *
 * @author cmooy
 */
trait HasOneReferralTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasOneReferralTraitConstructor()
	{
		//
	}

	/**
	 * call has one relationship
	 *
	 **/
	public function Referral()
	{
		return $this->hasOne('App\Entities\Referral', 'user_id');
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