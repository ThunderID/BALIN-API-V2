<?php

namespace App\Contracts\Policies;

use App\Entities\Customer;
use App\Entities\Referral;
use App\Entities\Voucher;

interface ProceedReferralSistemInterface
{
	public function storebonusesfordownline(Customer $customer, Referral $referral);
	
	public function storebonusesforupline(Referral $referral, Customer $customer);

	public function storequotaupline(Referral $referral, Customer $customer);

	public function storebonusesvoucher(Customer $customer, Voucher $voucher);

	public function storequotavoucher(Voucher $voucher, Customer $customer);
}
