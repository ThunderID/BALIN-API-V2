<?php

namespace App\Contracts\Policies;

use App\Entities\Voucher;

interface ProceedVoucherInterface
{
	public function storevoucher(array $voucher);

	public function storequota(Voucher $voucher, $quota);

	public function deletevoucher(Voucher $voucher);

	public function deletequota(Voucher $voucher);
}
