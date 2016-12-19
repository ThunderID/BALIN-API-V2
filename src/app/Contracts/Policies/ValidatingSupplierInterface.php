<?php

namespace App\Contracts\Policies;

use App\Entities\Supplier;

interface ValidatingSupplierInterface
{
	public function validatesupplier(array $supplier);
	
	public function validatedeletesupplier(Supplier $supplier);
}
