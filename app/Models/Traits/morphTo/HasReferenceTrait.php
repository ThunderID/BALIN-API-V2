<?php namespace App\Models\Traits\morphTo;

/**
 * Trait for models morph to reference.
 *
 * @author cmooy
 */
trait HasReferenceTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasReferenceTraitConstructor()
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
		return $this->belongsTo('\App\Models\Campaign', 'reference_id');
    }

	/**
	 * call reference in user
	 *
	 **/
    public function ReferenceReferral()
    {
		return $this->belongsTo('\App\Models\User', 'reference_id');
    }

	/**
	 * call reference in point
	 *
	 **/
    public function ReferencePointVoucher()
    {
		return $this->belongsTo('\App\Models\PointLog', 'point_log_id')->where('reference_type', 'App\Models\Voucher');
    }

	/**
	 * call reference in point
	 *
	 **/
    public function ReferencePointReferral()
    {
		return $this->belongsTo('\App\Models\PointLog', 'point_log_id')->where('reference_type', 'App\Models\User');
    }
}