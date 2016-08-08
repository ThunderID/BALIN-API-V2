<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ValidatingReferralSistemInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\ProceedReferralSistemInterface;
use App\Contracts\Policies\EffectTransactionInterface;

interface AddToCartInterface
{
	function __construct(ValidatingTransactionInterface $pre, ProceedTransactionInterface $pro, EffectTransactionInterface $post, ValidatingReferralSistemInterface $pre_voucher, ProceedReferralSistemInterface $pro_voucher);
	public function getError();
	public function getData();
	public function fill(array $sale);
	public function save();
}