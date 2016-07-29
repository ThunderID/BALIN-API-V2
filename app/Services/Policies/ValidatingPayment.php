<?php

namespace App\Services\Policies;

use App\Entities\Sale;

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
			$this->errors->add('Sale', 'Tidak dapat memproses pembayaran yang belum lunas');
		}
	}

	public function validatepaymentamount(Sale $sale, array $payment)
	{
		if($sale['bills'] != $payment['amount'])
		{
			$this->errors->add('Sale', 'Jumlah pembayaran tidak sesuai dengan tagihan');
		}
	}

	public function validatebillshavepaid(Sale $sale)
	{
		if($sale['bills'] > 0)
		{
			$this->errors->add('Sale', 'Tidak dapat memproses pembayaran untuk pembelian yang belum di lunas');
		}
	}
}

