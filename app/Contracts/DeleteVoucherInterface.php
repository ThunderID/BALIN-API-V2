<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingVoucherInterface;
use App\Contracts\Policies\ProceedVoucherInterface;
use App\Contracts\Policies\EffectVoucherInterface;

use App\Entities\Voucher;

interface DeleteVoucherInterface
{
	public function __construct(ValidatingVoucherInterface $pre, ProceedVoucherInterface $pro, EffectVoucherInterface $post);
	public function getError();
	public function getData();
	public function delete(Voucher $voucher);
}