<?php

namespace App\Services\Policies;

use App\Contracts\Policies\ValidatingSupplierInterface;

use Illuminate\Support\MessageBag;
use App\Entities\Supplier;
use App\Entities\Purchase;

class ValidatingSupplier implements ValidatingSupplierInterface
{
	public $errors;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 		= new MessageBag;
	}

	public function validatesupplier(array $supplier)
	{
		//
	}

	public function validatedeletesupplier(Supplier $supplier)
	{
		$used_supplier 		= Purchase::supplierid($supplier['id'])->count();

		if($used_supplier)
		{
			$this->errors->add('Product', 'Tidak dapat menghapus supplier yang pernah menyuplai');
		}
	}
}

