<?php

namespace App\Contracts\Policies;

use App\Entities\Customer;

interface ProceedRegisterUserInterface
{
	public function storeadmin(array $admin);
	
	public function storecustomer(array $customer);

	public function storereferral(Customer $customer, array $referral);
	
	public function activatinguser(Customer $customer);
}
