<?php

namespace App\Contracts\Policies;

use App\Entities\Customer;
use App\Entities\Referral;

interface ValidatingReferralSistemInterface
{
	public function validateupline(array $customer);

	public function validatedownline(Customer $customer);

	public function validatepromoreferral(array $customer);

	public function validateinvitation(Referral $referral, array $customer);

	public function validatepoint(array $point);
}
