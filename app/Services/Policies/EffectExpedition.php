<?php

namespace App\Services\Policies;

use App\Contracts\Policies\EffectExpeditionInterface;

use Illuminate\Support\MessageBag;

class EffectExpedition implements EffectExpeditionInterface
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

