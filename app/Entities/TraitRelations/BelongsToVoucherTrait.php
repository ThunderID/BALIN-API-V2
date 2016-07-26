<?php 

namespace App\Entities\TraitRelations;

trait BelongsToVoucherTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function BelongsToVoucherTraitConstructor()
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