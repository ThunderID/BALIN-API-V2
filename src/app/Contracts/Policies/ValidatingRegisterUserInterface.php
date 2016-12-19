<?php

namespace App\Contracts\Policies;

use App\Entities\Customer;

interface ValidatingRegisterUserInterface
{
	public function validateadmin(array $admin);

	public function validatecustomer(array $customer);

	public function validateactivationlink(array $customer);

	public function validateresetpassword(array $customer);
	
	public function validatechangepassword(array $customer);
	
	public function getreferralcode(array $customer);
	
	public function getactivationlink(array $customer);

	public function getresetpassword(Customer $customer);
}
