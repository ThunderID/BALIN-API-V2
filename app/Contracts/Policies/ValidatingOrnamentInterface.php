<?php

namespace App\Contracts\Policies;

use App\Entities\ProductExtension as Ornament;


interface ValidatingOrnamentInterface
{
	public function validateornament(array $ornament);

	public function validatedeleteornament(Ornament $ornament);
}
