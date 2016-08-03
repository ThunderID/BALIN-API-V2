<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingStoreSettingInterface;
use App\Contracts\Policies\ProceedStoreSettingInterface;
use App\Contracts\Policies\EffectStoreSettingInterface;

interface StoreSettingInterface
{
	public function __construct(ValidatingStoreSettingInterface $pre, ProceedStoreSettingInterface $pro, EffectStoreSettingInterface $post);
	public function getError();
	public function getData();
	public function fill(array $storesetting);
	public function save();
}