<?php

namespace App\Services\Policies;

use App\Entities\Voucher;
use App\Entities\PointLog;
use App\Entities\Sale;

use App\Contracts\Policies\ValidatingVoucherInterface;

use Illuminate\Support\MessageBag;

class ValidatingVoucher implements ValidatingVoucherInterface
{
	public $errors;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 		= new MessageBag;
	}

	public function validatevoucher(array $voucher)
	{
		//
	}

	public function getquota(array $voucher)
	{
		$exists_voucher		= Voucher::find($voucher['id']);

		if($exists_voucher)
		{
			$quota 			= $voucher['quota'] - $exists_voucher['quota'];
		}
		else
		{
			$quota 			= $voucher['quota'];
		}

		return $quota;
	}

	public function validatedeletevoucher(Voucher $voucher)
	{
		$used_point 		= PointLog::referenceid($voucher['id'])->count();

		if($used_point)
		{
			$this->errors->add('Product', 'Tidak dapat menghapus voucher yang telah digunakan');
		}

		$used_voucher 		= Sale::voucherid($voucher['id'])->count();
		
		if($used_voucher)
		{
			$this->errors->add('Product', 'Tidak dapat menghapus voucher yang telah digunakan');
		}
	}
}

