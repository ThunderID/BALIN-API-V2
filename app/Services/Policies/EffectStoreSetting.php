<?php

namespace App\Services\Policies;

use Illuminate\Support\MessageBag;

use App\Contracts\Policies\EffectStoreSettingInterface;

class EffectStoreSetting implements EffectStoreSettingInterface
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
