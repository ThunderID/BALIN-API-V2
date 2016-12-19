<?php

namespace App\Services\Policies;

use App\Contracts\Policies\EffectSupplierInterface;

use App\Entities\Supplier;

use Illuminate\Support\MessageBag;

class EffectSupplier implements EffectSupplierInterface
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
}

