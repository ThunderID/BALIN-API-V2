<?php

namespace App\Contracts\Policies;

interface EffectReferralSistemInterface
{
	public function sendmailpointreminder(array $point, array $product);
}
