<?php

namespace App\Services\Policies;

use App\Entities\Customer;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Hash;

use App\Contracts\Policies\ProceedCustomerInterface;

class ProceedCustomer implements ProceedCustomerInterface
{
	public $errors;
	public $customer;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 	= new MessageBag;
	}

	public function storecustomer(array $customer)
	{
		$stored_customer		= Customer::findornew($customer['id']);

		$stored_customer->fill($customer);

		if(Hash::needsRehash($stored_customer->password))
		{
			$stored_customer->password 	= Hash::make($stored_customer->password);
		}

		$stored_customer->role 	= 'customer';

		if(!$stored_customer->save())
		{
			$this->errors->add('Customer', $stored_customer->getError());
		}

		$this->customer		= $stored_customer;
	}
}
