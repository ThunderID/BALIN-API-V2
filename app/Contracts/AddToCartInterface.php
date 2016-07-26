<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingTransactionInterface;
use App\Contracts\Policies\ProceedTransactionInterface;
use App\Contracts\Policies\EffectTransactionInterface;

interface AddToCartInterface
{
	public function __construct(ValidatingTransactionInterface $pre, ProceedTransactionInterface $pro, EffectTransactionInterface $post);
	public function getError();
	public function getData();
	public function fill(array $sale);
	public function save();
}