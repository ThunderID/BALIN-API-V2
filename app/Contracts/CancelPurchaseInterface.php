<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingPurchaseInterface;
use App\Contracts\Policies\ProceedPurchaseInterface;
use App\Contracts\Policies\EffectPurchaseInterface;

interface CancelPurchaseInterface
{
	public function __construct(ValidatingPurchaseInterface $pre, ProceedPurchaseInterface $pro, EffectPurchaseInterface $post);
	public function getError();
	public function getData();
	public function fill(array $purchase);
	public function save();
}