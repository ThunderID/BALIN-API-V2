<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingVoucherInterface;
use App\Contracts\Policies\ProceedVoucherInterface;
use App\Contracts\Policies\EffectVoucherInterface;

interface StoreVoucherInterface
{
	public function __construct(ValidatingVoucherInterface $pre, ProceedVoucherInterface $pro, EffectVoucherInterface $post);
	public function getError();
	public function getData();
	public function fill(array $voucher);
	public function save();
}