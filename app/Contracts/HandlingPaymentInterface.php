<?php

namespace App\Contracts;

use App\Entities\Sale;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ValidatingPaymentInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\ProceedPaymentInterface;
use App\Contracts\Policies\EffectTransactionInterface;
use App\Contracts\Policies\EffectPaymentInterface;

interface HandlingPaymentInterface
{
	public function __construct(ValidatingTransactionInterface $pre_sale, ValidatingPaymentInterface $pre, ProceedTransactionInterface $pro_sale, ProceedPaymentInterface $pro, EffectTransactionInterface $post_sale, EffectPaymentInterface $post);
	public function getError();
	public function getData();
	public function fill(array $sale);
	public function save();
}