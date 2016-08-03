<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingSupplierInterface;
use App\Contracts\Policies\ProceedSupplierInterface;
use App\Contracts\Policies\EffectSupplierInterface;

use App\Entities\Supplier;

interface DeleteSupplierInterface
{
	public function __construct(ValidatingSupplierInterface $pre, ProceedSupplierInterface $pro, EffectSupplierInterface $post);
	public function getError();
	public function getData();
	public function delete(Supplier $supplier);
}