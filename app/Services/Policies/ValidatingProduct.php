<?php

namespace App\Services\Policies;

use App\Contracts\Policies\ValidatingProductInterface;

use App\Entities\Product;
use App\Entities\Varian;
use App\Entities\Price;
use App\Entities\ProductLabel;
use App\Entities\CategoryCluster;
use App\Entities\Image;

use Illuminate\Support\MessageBag;

class ValidatingProduct implements ValidatingProductInterface
{
	public $errors;

	public $product;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 	= new MessageBag;
	}

	public function validateproduct(array $product)
	{
		if(!isset($product['slug']))
		{
			$product['slug']	= Str::slug($product['name']);
		}

		$exists_product 		= Product::slug($product['slug'])->notid($product['id'])->first();

		if($exists_product)
		{
			$this->errors->add('Product', 'Produk sudah terdaftar');
		}

		$exists_product_2 		= Product::upc($product['upc'])->notid($product['id'])->first();

		if($exists_product_2)
		{
			$this->errors->add('Product', 'UPC produk sudah terdaftar');
		}
	}

	public function validatevarian(array $varian)
	{
		foreach ($varian as $key => $value) 
		{
			$exists_varian		= Varian::sku($value['sku'])->notid($value['id'])->first();

			if($exists_varian)
			{
				$this->errors->add('Product', 'Produk sudah terdaftar');
			}
		}
	}

	public function validateprice(array $price)
	{
	}

	public function validatelabel(array $label)
	{
	}

	public function validatecluster(array $cluster)
	{
		foreach ($cluster as $key => $value) 
		{
			$cluster 			= CategoryCluster::find($value['id']);

			if(!$cluster)
			{
				$this->errors->add('Product', 'Kategori / Tag tidak tersedia');
			}
		}
	}
	
	public function validateimage(array $image)
	{
	}

	public function validatedeleteproduct(Product $product)
	{
		foreach ($product['varians'] as $key => $value) 
		{
			if($value->transactiondetails()->count())
			{
				$this->errors->add('Product', 'Tidak dapat menghapus produk yang dijual');
			}
		}
	}
}

