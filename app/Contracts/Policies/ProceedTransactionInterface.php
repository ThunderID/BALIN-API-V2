<?php

namespace App\Contracts\Policies;

use App\Entities\Sale;
use App\Entities\Voucher;
use App\Entities\Purchase;
use App\Entities\Transaction;
use App\Entities\User;

interface ProceedTransactionInterface
{
	public function store(array $sale);

	public function storepurchase(array $purchase);

	public function storesaleitem(Sale $sale, array $transaction_details);

	public function storepurchaseitem(Purchase $sale, array $transaction_details);

	public function storepackingornament(Sale $sale, array $transaction_extensions);

	public function shippingaddress(Sale $sale, array $shipment);
	
	public function creditquotavoucher(Voucher $voucher, $quota, $notes);
	
	public function creditbalinpoint(User $user, Sale $sale, $pointdiscount);

	public function updatestatus(Transaction $sale, string $status);

	public function grantupline(Sale $sale);
}
