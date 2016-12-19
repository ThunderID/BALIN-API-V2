<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models morph to Owner.
 *
 * @author cmooy
 */
trait MorphToOwnerTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function MorphToOwnerTraitConstructor()
	{
		//
	}
	
	/**
	 * call morph to relationship
	 *
	 **/
    public function Owner()
    {
        return $this->morphTo();
    }
	
	/**
	 * call Owner in particular id
	 *
	 **/
    public function scopeOwnerID($query, $variable)
    {
		return $query->where('owner_id', $variable);
    }
	
	/**
	 * call Owner in particular type
	 *
	 **/
    public function scopeOwnerType($query, $variable)
    {
    	if(is_array($variable))
    	{
			return $query->whereIn('owner_type', $variable);
    	}

		return $query->where('owner_type', $variable);
    }

	/**
	 * call Owner in voucher
	 *
	 **/
    public function OwnerVoucher()
    {
		return $this->belongsTo('\App\Entities\VoucherCampaign', 'owner_id');
    }

	/**
	 * call Owner in user
	 *
	 **/
    public function OwnerReferral()
    {
		return $this->belongsTo('\App\Entities\User', 'owner_id');
    }

	/**
	 * call Owner in point
	 *
	 **/
    public function OwnerPointVoucher()
    {
		return $this->belongsTo('\App\Entities\PointLog', 'point_log_id')->where('owner_type', ['App\Models\Voucher', 'App\Entities\Voucher']);
    }

	/**
	 * call Owner in point
	 *
	 **/
    public function OwnerPointReferral()
    {
		return $this->belongsTo('\App\Entities\PointLog', 'point_log_id')->where('owner_type', ['App\Models\User', 'App\Entities\User']);
    }
}