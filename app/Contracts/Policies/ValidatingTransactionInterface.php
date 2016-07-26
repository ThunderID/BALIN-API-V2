<?php

namespace App\Contracts\Policies;

use App\Models\User;
use App\Models\Varian;
use App\Models\ProductExtension;
use App\Models\Courier;
use App\Models\ShippingCost;
use App\Models\Voucher;
use App\Entities\Sale;

interface ValidatingTransactionInterface
{
	public function validateprevioustransaction(User $user);
	public function validatebuyer(User $user);
	public function validatecheckoutstatus(Sale $sale);
	public function validatecheckedoutstatus(Sale $sale);
	
	public function validatesaleitem(array $transaction_details);
	public function validatestock(Varian $varian, $quantity);
	public function validateprice(Varian $varian, $price, $discount);
	public function calculatesubtotal($price, $discount, $quantity);

	public function validatepackingornament(array $transaction_extensions);
	public function validatestockornament(ProductExtension $ornament);
	public function calculatepackingcost($price);

	public function validateshippingaddress(array $shipment);
	public function validatecourier(Courier $courier);
	public function validatepostalcode(ShippingCost $postal_code);
	public function calculateshippingcost(ShippingCost $postal_code);

	public function validateshoppingvoucher(array $voucher);
	public function validatevoucher(Voucher $voucher);
	public function calculatevoucherdiscount(Voucher $voucher);

	public function calculatepointdiscount(User $user);
	public function calculatebills();
	
	public function getsalenumber(Sale $sale);
	public function getuniquenumber(Sale $sale);
	public function getsubtotal();
	public function getpackingcost();
	public function getshippingcost();
	public function getvoucherdiscount();
	public function getbills();
}
