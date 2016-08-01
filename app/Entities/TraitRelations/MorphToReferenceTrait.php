<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models morph to reference.
 *
 * @author cmooy
 */
trait MorphToReferenceTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function MorphToReferenceTraitConstructor()
	{
		//
	}
	
	/**
	 * call morph to relationship
	 *
	 **/
    public function reference()
    {
        return $this->morphTo();
    }
	
	/**
	 * call reference in particular id
	 *
	 **/
    public function scopeReferenceID($query, $variable)
    {
		return $query->where('reference_id', $variable);
    }
	
	/**
	 * call reference in particular type
	 *
	 **/
    public function scopeReferenceType($query, $variable)
    {
		return $query->where('reference_type', $variable);
    }

	/**
	 * call reference in voucher
	 *
	 **/
    public function ReferenceVoucher()
    {
		return $this->belongsTo('\App\Entities\VoucherCampaign', 'reference_id');
    }

	/**
	 * call reference in user
	 *
	 **/
    public function ReferenceReferral()
    {
		return $this->belongsTo('\App\Entities\User', 'reference_id');
    }

	/**
	 * call reference in point
	 *
	 **/
    public function ReferencePointVoucher()
    {
		return $this->belongsTo('\App\Entities\PointLog', 'point_log_id')->where('reference_type', ['App\Models\Voucher', 'App\Entities\Voucher']);
    }

	/**
	 * call reference in point
	 *
	 **/
    public function ReferencePointReferral()
    {
		return $this->belongsTo('\App\Entities\PointLog', 'point_log_id')->where('reference_type', ['App\Models\User', 'App\Entities\User']);
    }
}