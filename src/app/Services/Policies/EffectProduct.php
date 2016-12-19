<?php

namespace App\Services\Policies;

use App\Contracts\Policies\EffectProductInterface;

use Illuminate\Support\MessageBag;

class EffectProduct implements EffectProductInterface
{
	public $errors;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 	= new MessageBag;
	}
}

