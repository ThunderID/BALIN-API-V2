<?php

namespace App\Services\Policies;

use App\Models\User;
use App\Entities\Sale;
use App\Models\TransactionLog;
use App\Models\Varian;
use App\Models\ProductExtension;
use App\Models\Courier;
use App\Models\ShippingCost;
use App\Models\Voucher;
use App\Models\StoreSetting;

use App\Contracts\Policies\ValidatingPaymentInterface;

use Illuminate\Support\MessageBag;

class ValidatingPayment implements ValidatingPaymentInterface
{
	public $errors;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 	= new MessageBag;
	}

	public function validatebillshaventpaid(Sale $sale)
	{
		if($sale['bills'] <= 0)
		{
			$this->errors->add('Sale', 'Tidak dapat memproses pembayaran untuk pembelian yang belum di checkout');
		}
	}

	public function validatepaymentamount(Sale $sale, array $payment)
	{
		if($sale['bills'] != $payment['amount'])
		{
			$this->errors->add('Sale', 'Jumlah pembayaran tidak sesuai dengan tagihan');
		}
	}
}

