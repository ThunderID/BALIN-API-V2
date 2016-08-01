<?php

namespace App\Contracts\Policies;

use App\Entities\Customer;

interface ValidatingRegisterUserInterface
{
	public function validatecustomer(array $customer);

	public function validateactivationlink(array $customer);
	
	public function getreferralcode(array $customer);
	
	public function getactivationlink(array $customer);
}
