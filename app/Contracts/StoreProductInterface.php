<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingProductInterface;
use App\Contracts\Policies\ProceedProductInterface;
use App\Contracts\Policies\EffectProductInterface;

interface StoreProductInterface
{
	public function __construct(ValidatingProductInterface $pre, ProceedProductInterface $pro, EffectProductInterface $post);
	public function getError();
	public function getData();
	public function fill(array $customer);
	public function save();
}