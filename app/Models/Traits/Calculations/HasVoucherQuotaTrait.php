<?php 

namespace App\Models\Traits\Calculations;

use Validator;
use App\Models\StoreSetting;
use App\Models\PointLog;

/**
 * Function to do calculations
 *
 * @author cmooy
 */
trait HasVoucherQuotaTrait 
{
	/**
	 * Do credit quota of voucher.
	 *
	 * @param model voucher, message
	 * @return boolean
	 */
	public function CreditQuota($voucher, $message)
	{
		$quotalog                       = new \App\Models\QuotaLog;

		$quotalog->fill([
				'voucher_id'            => $voucher['id'],
				'amount'                => -1,
				'notes'                 => $message,
			]);

		if(!$quotalog->save())
		{
			$this->errors	 			= $quotalog->getError();
			
			return false;
		}

		return true;
	}

	/**
	 * Do count transaction voucher discount
	 *
	 * @param model transaction
	 * @return voucher discount
	 */
	public function CountVoucherDiscount($transaction)
	{
		$voucher_discount 					= 0;

		$voucherrules                       = ['started_at' => 'before:'.$transaction->transact_at->format('Y-m-d H:i:s'), 'expired_at' => 'after:'.$transaction->transact_at->format('Y-m-d H:i:s')];

		$quota 								= \App\Models\QuotaLog::voucherid($transaction->voucher_id)->ondate([$transaction->transact_at->format('Y-m-d H:i:s'), null])->sum('amount');

		if($quota <= 0)
		{
			$this->errors 					= 'Voucher tidak dapat digunakan.';

			return false;
		}

		if($transaction->voucher()->count() && $transaction->status=='paid')
		{
			$validator                      = Validator::make($transaction->voucher['attributes'], $voucherrules);

			if (!$validator->passes())
			{
				$this->errors 				= 'Voucher Tidak dapat digunakan.';

				return false;
			}

			switch($transaction->voucher['attributes']['type'])
			{
				case 'debit_point' :
					$result					= $transaction->DebitPoint($transaction, $transaction->voucher->value);
					if(!$result)
					{
						$this->errors 		= 'Cannot debit point';

						return false;
					}
				break;
				default :
				break;
			}
		}
		elseif($transaction->voucher()->count())
		{
			$validator                      = Validator::make($transaction->voucher['attributes'], $voucherrules);

			if (!$validator->passes())
			{
				$this->errors 				= 'Voucher Tidak dapat digunakan.';

				return false;
			}

			switch($transaction->voucher['attributes']['type'])
			{
				case 'free_shipping_cost' :
					$voucher_discount		= (!is_null($transaction->shipping_cost) ? $transaction->shipping_cost : 0);
				break;
				default :
				break;
			}
		}

		return $voucher_discount;
	}

	/**
	 * Debit Point from voucher
	 *
	 * @param model transaction
	 * @return voucher discount
	 */
	public function DebitPoint($transaction, $debit)
	{
		if(!is_null($transaction->id))
		{
			$expired                        = StoreSetting::type('voucher_point_expired')->Ondate('now')->first();
			$previous                       = PointLog::referenceid($transaction->id)->referencetype('App\Models\Transaction')->first();

			if($expired && !$previous)
			{
				$point                      = new PointLog;
				$point->fill([
						'user_id'           => $transaction->user_id,
						'amount'            => $debit,
						'expired_at'        => date('Y-m-d H:i:s', strtotime($transaction->transact_at.' '.$expired->value)),
						'notes'             => 'Bonus Belanja dengan Voucher ',
					]);

				$point->reference()->associate($transaction);

				if(!$point->save())
				{
					$this->errors               = $point->getError();

					return false;
				}
			}
		}

		return $result;
	}
}