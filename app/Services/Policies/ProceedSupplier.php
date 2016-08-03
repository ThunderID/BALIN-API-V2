<?php

namespace App\Services\Policies;

use App\Contracts\Policies\ProceedSupplierInterface;

use App\Entities\Supplier;

use Illuminate\Support\MessageBag;

class ProceedSupplier implements ProceedSupplierInterface
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

	public function storesupplier(array $supplier)
	{
		$stored_supplier					= Supplier::findornew($supplier['id']);
		
		$stored_supplier->fill($supplier);

		if(!$stored_supplier->save())
		{
			$this->errors->add('Supplier', $stored_supplier->getError());
		}
	}

	public function deletesupplier(Supplier $supplier)
	{
		if(!$supplier->delete())
		{
			$this->errors->add('Supplier', $supplier->getError());
		}
	}
}

