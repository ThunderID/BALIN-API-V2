<?php

namespace App\Contracts\Policies;

use App\Entities\Customer;

interface EffectReferralSistemInterface
{
	public function sendmailpointreminder(array $point, array $product);

	public function sendinvitationmail(Customer $customer, $email);
}
