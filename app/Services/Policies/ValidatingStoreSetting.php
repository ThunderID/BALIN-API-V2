<?php

namespace App\Services\Policies;

use App\Entities\Store;
use App\Entities\Slider;
use App\Entities\Policy;
use App\Entities\StorePage;

use App\Contracts\Policies\ValidatingStoreSettingInterface;

use Illuminate\Support\MessageBag;

class ValidatingStoreSetting implements ValidatingStoreSettingInterface
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

	public function validatestore(array $store)
	{
		//
	}

	public function validatestorepage(array $storepage)
	{
		//
	}

	public function validateslider(array $slider)
	{
		//
	}

	public function validatepolicy(array $policy)
	{
		//
	}
}

