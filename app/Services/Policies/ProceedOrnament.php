<?php

namespace App\Services\Policies;

use App\Entities\ProductExtension as Ornament;
use App\Entities\QuotaLog;

use Illuminate\Support\MessageBag;

use App\Contracts\Policies\ProceedOrnamentInterface;

class ProceedOrnament implements ProceedOrnamentInterface
{
	public $errors;

	public $ornament;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 		= new MessageBag;
	}

	public function storeornament(array $ornament)
	{
		$stored_ornament					= Ornament::findornew($ornament['id']);
		
		$stored_ornament->fill($ornament);

		if(!$stored_ornament->save())
		{
			$this->errors->add('Ornament', $stored_ornament->getError());
		}

		$this->ornament 					= $stored_ornament;
	}

	public function deleteornament(Ornament $ornament)
	{
		if(!$ornament->delete())
		{
			$this->errors->add('Ornament', $ornament->getError());
		}
	}

}
