<?php

namespace App\Contracts\Policies;

use App\Entities\Supplier;

interface ProceedSupplierInterface
{
	public function storesupplier(array $supplier);

	public function deletesupplier(Supplier $supplier);
}
