<?php

namespace App\Contracts\Policies;

use App\Entities\Supplier;

interface ProceedSupplierInterface
{
	public function storesupplier(array $supplier);

	public function storeimage(Supplier $supplier, array $image);

	public function deletesupplier(Supplier $supplier);

	public function deleteimage(Supplier $supplier);
}
