<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\ValidatingPaymentInterface;
use App\Contracts\Policies\ProceedPaymentInterface;
use App\Contracts\Policies\EffectPaymentInterface;

interface ProcessingPaymentInterface
{
	public function __construct(ValidatingTransactionInterface $pre_sale, ProceedTransactionInterface $pro_sale, ValidatingPaymentInterface $pre, ProceedPaymentInterface $pro, EffectPaymentInterface $post);
	public function getError();
	public function getData();
	public function fill(array $sale);
	public function save();
}