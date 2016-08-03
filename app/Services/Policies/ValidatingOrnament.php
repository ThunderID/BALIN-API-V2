<?php

namespace App\Services\Policies;

use App\Entities\ProductExtension as Ornament;
use App\Entities\TransactionExtension;

use App\Contracts\Policies\ValidatingOrnamentInterface;

use Illuminate\Support\MessageBag;

class ValidatingOrnament implements ValidatingOrnamentInterface
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

	public function validateornament(array $ornament)
	{
		//
	}

	public function validatedeleteornament(Ornament $ornament)
	{
		//
		$used_ornament 		= TransactionExtension::ProductExtensionID($ornament['id'])->count();
		
		if($used_ornament)
		{
			$this->errors->add('Ornament', 'Tidak dapat menghapus ornament yang telah digunakan');
		}
	}
}

