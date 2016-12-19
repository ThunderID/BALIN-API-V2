<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingOrnamentInterface;
use App\Contracts\Policies\ProceedOrnamentInterface;
use App\Contracts\Policies\EffectOrnamentInterface;

interface StoreOrnamentInterface
{
	public function __construct(ValidatingOrnamentInterface $pre, ProceedOrnamentInterface $pro, EffectOrnamentInterface $post);
	public function getError();
	public function getData();
	public function fill(array $ornament);
	public function save();
}