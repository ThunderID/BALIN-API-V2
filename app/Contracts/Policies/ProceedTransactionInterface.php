<?php

namespace App\Contracts\Policies;

use App\Entities\Sale;
use App\Models\Voucher;
use App\Models\User;

interface ProceedTransactionInterface
{
	public function store(array $sale);

	public function storesaleitem(Sale $sale, array $transaction_details);

	public function storepackingornament(Sale $sale, array $transaction_extensions);

	public function shippingaddress(Sale $sale, array $shipment);
	
	public function creditquotavoucher(Voucher $voucher, $quota, $notes);
	
	public function creditbalinpoint(User $user, Sale $sale, $pointdiscount);

	public function updatestatus(Sale $sale, string $status);
}
