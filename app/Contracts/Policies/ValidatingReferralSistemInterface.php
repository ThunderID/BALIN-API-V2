<?php

namespace App\Contracts\Policies;

use App\Entities\Customer;

interface ValidatingReferralSistemInterface
{
	public function validateupline(array $customer);

	public function validatedownline(Customer $customer);

	public function validatepromoreferral(array $customer);
}
