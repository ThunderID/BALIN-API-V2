<?php

namespace App\Services\Policies;

use Illuminate\Support\MessageBag;

use App\Contracts\Policies\EffectOrnamentInterface;

class EffectOrnament implements EffectOrnamentInterface
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
