<?php

namespace App\Services\Policies;

use App\Entities\User;
use App\Entities\Customer;
use App\Entities\Sale;
use App\Entities\Purchase;
use App\Entities\TransactionLog;
use App\Entities\Varian;
use App\Entities\ProductExtension;
use App\Entities\Courier;
use App\Entities\ShippingCost;
use App\Entities\Supplier;
use App\Entities\Voucher;
use App\Entities\StoreSetting;

use App\Contracts\Policies\ValidatingTransactionInterface;

use Illuminate\Support\MessageBag;

class ValidatingTransaction implements ValidatingTransactionInterface
{
	public $errors;

	protected $subtotal = 0;
	protected $packingcost = 0;
	protected $shippingcost = 0;
	protected $voucherdiscount = 0;
	protected $pointdiscount = 0;
	protected $paymentamount = 0;
	protected $uniquenumber = 0;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 	= new MessageBag;
	}

	public function validateprevioustransaction(User $user)
	{
		$prev_sale 					= Sale::status(['cart', 'na'])->userid($user->id)->get();

		if($prev_sale->count())
		{
			foreach ($prev_sale as $key => $value) 
			{
				//do cancel
				$transaction_log 	= new TransactionLog;
				$transaction_log	= $transaction_log->fill(['status' => 'abandoned', 'transaction_id' => $value['id']]);

				if(!$transaction_log->save())
				{
					$this->errors->add('Abandoned', 'Masih ada transaksi yang belum di checkout.');
				}
			}
		}
	}

	public function validatebuyer(User $user)
	{
		if(!$user)
		{
			$this->errors->add('Customer', 'Klien tidak valid');
		}
	}
	
	public function validatesupplier(Supplier $supplier)
	{
		if(!$supplier)
		{
			$this->errors->add('Supplier', 'Supplier tidak valid');
		}
	}

	public function validatecheckoutstatus(Sale $sale)
	{
		if(!in_array($sale['status'], ['na', 'cart', 'wait']))
		{
			$this->errors->add('Sale', 'Tidak dapat checkout belanja');
		}
	}

	public function validatecheckedoutstatus(Sale $sale)
	{
		if(!in_array($sale['status'], ['wait']))
		{
			$this->errors->add('Sale', 'Tidak dapat memproses pembayaran untuk pembelian yang belum di checkout');
		}
	}

	public function validatesaleitem(array $transaction_details)
	{
		foreach ($transaction_details as $key => $value) 
		{
			$varian 				= Varian::find($value['varian_id']);

			$this->validatestock($varian, $value['quantity']);
			$this->validateprice($varian, $value['price'], $value['discount']);
			$this->calculatesubtotal($value['price'], $value['discount'], $value['quantity']);
		}

		if(empty($transaction_details))
		{
			$this->errors->add('Sale', 'Tidak ada item di keranjang belanja');
		}
	}

	public function validaterollbackitem(array $transaction_details)
	{
		foreach ($transaction_details as $key => $value) 
		{
			$varian 				= Varian::find($value['varian_id']);

			if($value['quantity'] > $varian->current_stock)
			{
				$this->errors->add('Purchase', 'Stok digudang Produk '.$varian->product->name.' ukuran '.$varian->size.' tidak sesuai dengan pembatalan transaksi');
			}
		}
	}
	
	public function validatepurchaseitem(array $transaction_details)
	{
		foreach ($transaction_details as $key => $value) 
		{
			$varian 				= Varian::find($value['varian_id']);

			if(!$varian)
			{
				$this->errors->add('Purchase', 'Item tidak valid');
			}
		}

		if(empty($transaction_details))
		{
			$this->errors->add('Purchase', 'Tidak ada item di keranjang belanja');
		}
	}

	public function validatestock(Varian $varian, $quantity)
	{
		if($varian->current_stock < $quantity)
		{
			$this->errors->add('Stock', 'Stok '.$varian->product->name.' Ukuran '.$varian['size'].' tinggal '.$varian->current_stock);
		}
	}
	
	public function validateprice(Varian $varian, $price, $discount)
	{
		if($varian->product->price != $price)
		{
			$this->errors->add('Price', 'Harga '.$varian->product->name.' tidak sesuai dengan harga saat ini');
		}

		if($varian->product->promo_price != 0 && ($varian->product->price - $varian->product->promo_price) != $discount)
		{
			$this->errors->add('Price', 'Harga '.$varian->product->name.' tidak sesuai dengan harga saat ini');
		}
	}
		
	public function calculatesubtotal($price, $discount, $quantity)
	{
		$this->subtotal 		= $this->subtotal + (($price - $discount) * $quantity);
	}

	public function validatepackingornament(array $transaction_extensions)
	{
		foreach ($transaction_extensions as $key => $value) 
		{
			$ornament			= ProductExtension::find($value['product_extension_id']);

			$this->validatestockornament($ornament);
			$this->calculatepackingcost($ornament['price']);
		}
	}

	public function validatestockornament(ProductExtension $ornament)
	{
		if(!$ornament->is_active)
		{
			$this->errors->add('Stock', 'Stok '.$ornament->name.' tidak tersedia ');
		}
	}
			
	public function calculatepackingcost($price)
	{
		$this->packingcost 		= $this->packingcost + $price;
	}
	
	public function validateshippingaddress(array $shipment)
	{
		$courier 				= Courier::id($shipment['courier_id'])->first();

		$this->validatecourier($courier);

		$postal_code			= ShippingCost::courierid($courier->id)->postalcode($shipment['address']['zipcode'])->first();
	
		$this->validatepostalcode($postal_code);

		$this->calculateshippingcost($postal_code);
		
	}

	public function validatecourier(Courier $courier)
	{
		if(!$courier)
		{
			$this->errors->add('Courier', 'Kurir tidak valid');
		}
	}

	public function validatepostalcode(ShippingCost $postal_code)
	{
		if(!$postal_code)
		{
			$this->errors->add('Address', 'Kode pos tidak tersedia dalam jalur pengiriman');
		}
	}
			
	public function calculateshippingcost(ShippingCost $postal_code)
	{
		$this->shippingcost		= $this->shippingcost + $postal_code['cost'];
	}

	public function validateshoppingvoucher(array $voucher)
	{
		if(isset($voucher['code']))
		{
			$voucher 		= Voucher::code($voucher['code'])->type(['free_shipping_cost', 'promo_referral'])->ondate('now')->first();
			// $voucher 		= Voucher::code($voucher['code'])->type(['free_shipping_cost', 'debit_point'])->ondate('now')->first();

			$this->validatevoucher($voucher);

			$this->calculatevoucherdiscount($voucher);
		}
	}

	public function validatevoucher(Voucher $voucher)
	{
		if(!$voucher)
		{
			$this->errors->add('Voucher', 'Voucher tidak valid');
		}

		if($voucher->quota <= 0)
		{
			$this->errors->add('Voucher', 'Quota voucher tidak tersedia');
		}
	}

	public function calculatevoucherdiscount(Voucher $voucher)
	{
		if($voucher['type'] == 'free_shipping_cost')
		{
			$this->voucherdiscount 	= $this->shippingcost;
		}
	}

	public function calculatepointdiscount(Customer $user, Sale $sale)
	{
		if($sale->count())
		{
			$bills		= $sale->bills + $sale->unique_number;
		}
		else
		{
			$bills 		= $this->getbills();
		}
	
		$point 			= $user->total_point;

		if($point >= $bills)
		{
			$this->pointdiscount 	= $bills;
		}
		elseif($point < $bills && $point > 0)
		{
			$this->pointdiscount	= $point;
		}
	}

	public function calculatebills()
	{
		$this->bills	= $this->subtotal + $this->packingcost + $this->shippingcost - $this->voucherdiscount - $this->pointdiscount - $this->paymentamount;

		if($this->bills > 0)
		{
			$this->bills = $this->bills  - $this->uniquenumber;
		}
	}

	public function getsalenumber(Sale $sale)
	{
		if(!empty($sale['ref_number']))
		{
			return $sale['ref_number'];
		}

		$prefix			= 's'.date("ym");

		$latest_sale	= Sale::select('ref_number')
								->where('ref_number', 'like', $prefix.'%')
								->status(['wait', 'veritrans_processing_payment', 'paid', 'packed', 'shipping', 'delivered', 'canceled'])
								->orderBy('ref_number', 'DESC')
								->first();

		if(empty($latest_sale))
		{
			$number		= 1;
		}
		else
		{
			$number		= 1 + (int)substr($latest_sale['ref_number'],5);
		}

		return $prefix . str_pad($number,4,"0",STR_PAD_LEFT);
	}
	
	public function getpurchasenumber(Purchase $purchase)
	{
		if(!empty($purchase['ref_number']))
		{
			return $purchase['ref_number'];
		}
		
		$prefix			= 'b'.date("ym");

		$latest_sale	= Purchase::select('ref_number')
								->where('ref_number', 'like', $prefix.'%')
								->status(['wait', 'veritrans_processing_payment', 'paid', 'packed', 'shipping', 'delivered', 'canceled'])
								->orderBy('ref_number', 'DESC')
								->first();

		if(empty($latest_sale))
		{
			$number		= 1;
		}
		else
		{
			$number		= 1 + (int)substr($latest_sale['ref_number'],5);
		}

		return $prefix . str_pad($number,4,"0",STR_PAD_LEFT);
	}
	
	public function getuniquenumber(Sale $sale)
	{
		$this->calculatebills();

		if($this->bills > 0)
		{
			$i							= 0;
			$amount						= true;

			while($amount)
			{
				$prev_number			= Sale::orderBy('id', 'DESC')->status(['wait', 'veritrans_processing_payment'])->first();

				$limit					= StoreSetting::type('limit_unique_number')->ondate('now')->first();

				if($prev_number['unique_number'] < $limit['value'])
				{
					$unique_number		= $i+ $prev_number['unique_number'] + 1;
				}
				else
				{
					$unique_number		= $i+ 1;
				}

				$amount					= Sale::amount($this->bills - $unique_number)->status(['wait', 'veritrans_processing_payment'])->notid($sale->id)->first();
				$i						= $i+1;
			}

			return $unique_number;
		}
		else
		{
			return 0;
		}
	}

	public function getsubtotal()
	{
		return $this->subtotal;
	}

	public function getpackingcost()
	{
		return $this->packingcost;
	}

	public function getshippingcost()
	{
		return $this->shippingcost;
	}

	public function getvoucherdiscount()
	{
		return $this->voucherdiscount;
	}

	public function getpointdiscount()
	{
		return $this->pointdiscount;
	}

	public function getbills()
	{
		$this->calculatebills();

		return $this->bills;
	}
}

