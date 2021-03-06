<?php

namespace App\Contracts\Policies;

use App\Entities\Customer;

interface EffectRegisterUserInterface
{
	public function sendactivationmail(Customer $customer);
	
	public function sendresetpasswordmail(Customer $customer);

	public function contactusmail(array $customer);
}
