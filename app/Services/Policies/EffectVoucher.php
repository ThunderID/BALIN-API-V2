<?php

namespace App\Services\Policies;

use App\Entities\Voucher;
use App\Entities\QuotaLog;

use Illuminate\Support\MessageBag;

use App\Contracts\Policies\EffectVoucherInterface;

class EffectVoucher implements EffectVoucherInterface
{
	public $errors;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 		= new MessageBag;
	}
}
