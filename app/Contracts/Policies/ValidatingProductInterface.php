<?php

namespace App\Contracts\Policies;

interface ValidatingProductInterface
{
	public function validateproduct(array $product);

	public function validatevarian(array $varian);
	
	public function validateprice(array $price);

	public function validatelabel(array $label);

	public function validatecluster(array $cluster);

	public function validateimage(array $image);
}
