<?php

namespace App\Contracts\Policies;

use App\Entities\Voucher;

interface ValidatingVoucherInterface
{
	public function validatevoucher(array $voucher);

	public function getquota(array $voucher);

	public function validatedeletevoucher(Voucher $voucher);
}
