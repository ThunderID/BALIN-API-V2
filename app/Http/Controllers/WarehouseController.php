<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;

/**
 * Handle Protected Warehouse's report
 * 
 * @author cmooy
 */
class WarehouseController extends Controller
{
	/**
	 * Display product stock's movement
	 *
	 * @param skip, take
	 * @return Response
	 */
	public function card($id = null)
	{
		$varian                     = \App\Models\Varian::id($id);

		$detail                     = \App\Models\TransactionDetail::varianid($id)->stockmovement(true);

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'ondate':
						if(is_array($value))
						{
							$balance	= \App\Models\Varian::id($id)->TransactionLogChangedAt($value[0]);
							$prev_date 	= $value[0];
							$detail     = $detail->TransactionLogChangedAt($value);
							$varian     = $varian->TransactionLogChangedAt($value[1]);
						}
						else
						{
							$detail     = $detail->TransactionLogChangedAt($value);
							$varian     = $varian->TransactionLogChangedAt($value);
						}
						break;
					default:
						# code...
						break;
				}
			}
		}
   
   		if(!$varian->first())
   		{
        	return new JSend('error', (array)Input::all(), 'ID Tidak valid.');
   		}

		$varian                     = $varian->with(['product'])->first()->toArray();

		if(isset($balance))
		{
			$balance 				= $balance->first();
			$balance_old[] 			= ['ref' => 'Balance','varian_id' => $id, 'transact_at' => Carbon::parse($prev_date)->format('Y-m-d H:i:s'), 'stock_in' => $balance['inventory_stock'], 'stock_out' => 0];
			$detail_old 			= $detail->get()->toArray();

			if(!empty($detail_old))
			{
				$varian['details'] 	= array_merge($balance_old, $detail_old);
			}
			else
			{
				$varian['details'] 	= $balance_old;
			}
		}
		else
		{
			$varian['details']		= $detail->get()->toArray();
		}

		return new JSend('success', (array)$varian);
	}

	/**
	 * Display products critical stock
	 *
	 * @param skip, take
	 * @return Response
	 */
	public function critical()
	{
		$setting                    = \App\Models\Policy::ondate('now')->type('critical_stock')->first();

		if(!$setting)
		{
			$critical               = 0;
		}
		else
		{
			$critical               = 0 - $setting['value'];
		}

		$result                     = \App\Models\Varian::critical($critical);
		
		$count                      = count($result->get(['id']));

		if(Input::has('skip'))
		{
			$skip                   = Input::get('skip');
			$result                 = $result->skip($skip);
		}

		if(Input::has('take'))
		{
			$take                   = Input::get('take');
			$result                 = $result->take($take);
		}

		$result                     = $result->with(['product'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display products critical stock
	 *
	 * @param skip, take
	 * @return Response
	 */
	public function opname()
	{
		$result                     = new \App\Models\Varian;

		if(Input::has('sort'))
		{
			$sort                 = Input::get('sort');

			foreach ($sort as $key => $value) 
			{
				if(!in_array($value, ['asc', 'desc']))
				{
					return new JSend('error', (array)Input::all(), $key.' harus bernilai asc atau desc.');
				}
				switch (strtolower($key)) 
				{
					case 'stockinventory':
						$result->sort 			= 'inventory_stock';
						$result->sort_param 	= $value;
						break;
					case 'stockout':
						$result->sort 			= 'sold_item';
						$result->sort_param 	= $value;
						break;
					default:
						# code...
						break;
				}
			}
		}

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'size':
						$result     = $result->size($value);
						break;
					case 'ondate':
						$result     = $result->TransactionLogChangedAt($value);
						break;
					case 'name':
						$result     = $result->ProductName($value);
						break;
					default:
						# code...
						break;
				}
			}
		}

		if(Input::has('skip'))
		{
			$skip                   = Input::get('skip');
			$result                 = $result->skip($skip);
		}

		if(Input::has('take'))
		{
			$take                   = Input::get('take');
			$result                 = $result->take($take);
		}

		$count                      = count($result->get(['id']));

		$result                     = $result->with(['product'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}
}
