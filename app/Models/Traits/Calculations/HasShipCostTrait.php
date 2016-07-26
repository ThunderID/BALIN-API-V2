<?php 

namespace App\Models\Traits\Calculations;

Use App\Models\StoreSetting;

/**
 * Function to do calculations for shipping cost
 *
 * @author cmooy
 */
trait HasShipCostTrait 
{
	/**
	 * Check shipping cost.
	 *
	 * @param array of model transactiondetails, cost
	 * @return shipping cost
	 */
	public function CountShippingCost($transactiondetails, $cost)
	{
		$qty                                    = 0;
		foreach ($transactiondetails as $key => $value) 
		{
			$qty                                = $qty + $value['quantity'];
		}

		$default                                = StoreSetting::type('item_for_one_package')->ondate('now')->orderby('created_at', 'desc')->first();

		if(!$default)
		{
			$max_item                           = 1;
		}
		else
		{
			$max_item                           = $default['value'];
		}

		return $cost * ceil($qty/$max_item);
	}
}