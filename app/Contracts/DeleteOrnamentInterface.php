<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingOrnamentInterface;
use App\Contracts\Policies\ProceedOrnamentInterface;
use App\Contracts\Policies\EffectOrnamentInterface;

use App\Entities\ProductExtension as Ornament;

interface DeleteOrnamentInterface
{
	public function __construct(ValidatingOrnamentInterface $pre, ProceedOrnamentInterface $pro, EffectOrnamentInterface $post);
	public function getError();
	public function getData();
	public function delete(Ornament $Ornament);
}