<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingExpeditionInterface;
use App\Contracts\Policies\ProceedExpeditionInterface;
use App\Contracts\Policies\EffectExpeditionInterface;

interface StoreExpeditionInterface
{
	public function __construct(ValidatingExpeditionInterface $pre, ProceedExpeditionInterface $pro, EffectExpeditionInterface $post);
	public function getError();
	public function getData();
	public function fill(array $courier);
	public function save();
}