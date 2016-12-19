<?php

namespace App\Contracts\Policies;

use App\Entities\ProductExtension as Ornament;

interface ProceedOrnamentInterface
{
	public function storeornament(array $ornament);
}