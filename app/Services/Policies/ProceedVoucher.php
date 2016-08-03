<?php

namespace App\Services\Policies;

use App\Entities\Voucher;
use App\Entities\QuotaLog;

use Illuminate\Support\MessageBag;

use App\Contracts\Policies\ProceedVoucherInterface;

class ProceedVoucher implements ProceedVoucherInterface
{
	public $errors;

	public $voucher;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 		= new MessageBag;
	}

	public function storevoucher(array $voucher)
	{
		$stored_voucher					= Voucher::findornew($voucher['id']);
		
		$stored_voucher->fill($voucher);

		if(!$stored_voucher->save())
		{
			$this->errors->add('Voucher', $stored_voucher->getError());
		}

		$this->voucher 					= $stored_voucher;
	}

	public function storequota(Voucher $voucher, $quota)
	{
		if($quota!=0)
		{
			$stored_quota					= new QuotaLog;
			
			$stored_quota->fill(['voucher_id' => $voucher['id'], 'amount' => $quota]);

			if(!$stored_quota->save())
			{
				$this->errors->add('Voucher', $stored_quota->getError());
			}
		}

		$this->voucher 					= Voucher::findornew($voucher['id']);
	}

	public function deletevoucher(Voucher $voucher)
	{
		if(!$voucher->delete())
		{
			$this->errors->add('Voucher', $voucher->getError());
		}
	}

	public function deletequota(Voucher $voucher)
	{
		foreach ($voucher->quotas as $key => $value) 
		{
			if(!$value->delete())
			{
				$this->errors->add('Voucher', $value->getError());
			}
		}
		
	}
}
