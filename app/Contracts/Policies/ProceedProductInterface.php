<?php

namespace App\Contracts\Policies;

use App\Entities\Product;

interface ProceedProductInterface
{
	public function storeproduct(array $product);

	public function storevarian(Product $product, array $varian);
	
	public function storeprice(Product $product, array $price);

	public function storelabel(Product $product, array $label);

	public function storecluster(Product $product, array $cluster);

	public function storeimage(Product $product, array $image);
}