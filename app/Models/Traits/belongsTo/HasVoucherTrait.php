<?php namespace App\Models\Traits\belongsTo;

trait HasVoucherTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasVoucherTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Voucher()
	{
		return $this->belongsTo('App\Models\Voucher');
	}

	public function scopeHasVoucher($query, $variable)
	{
		return $query->whereHas('voucher', function($q)use($variable){$q;});
	}

	public function scopeVoucherID($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn('voucher_id', $variable);
		}

		return $query->where('voucher_id', $variable);
	}
}