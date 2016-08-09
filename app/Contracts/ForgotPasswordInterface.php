<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingRegisterUserInterface;
use App\Contracts\Policies\ProceedRegisterUserInterface;
use App\Contracts\Policies\EffectRegisterUserInterface;

interface ForgotPasswordInterface
{
	public function __construct(ValidatingRegisterUserInterface $pre, ProceedRegisterUserInterface $pro, EffectRegisterUserInterface $post);
	public function getError();
	public function getData();
	public function fill(array $customer);
	public function save();
}