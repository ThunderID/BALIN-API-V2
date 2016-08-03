<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingSupplierInterface;
use App\Contracts\Policies\ProceedSupplierInterface;
use App\Contracts\Policies\EffectSupplierInterface;

interface StoreSupplierInterface
{
	public function __construct(ValidatingSupplierInterface $pre, ProceedSupplierInterface $pro, EffectSupplierInterface $post);
	public function getError();
	public function getData();
	public function fill(array $supplier);
	public function save();
}