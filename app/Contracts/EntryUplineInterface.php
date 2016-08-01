<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingReferralSistemInterface;
use App\Contracts\Policies\ProceedReferralSistemInterface;
use App\Contracts\Policies\EffectReferralSistemInterface;

interface EntryUplineInterface
{
	public function __construct(ValidatingReferralSistemInterface $pre, ProceedReferralSistemInterface $pro, EffectReferralSistemInterface $post);
	public function getError();
	public function getData();
	public function fill(array $customer);
	public function save();
}