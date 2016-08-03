<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingProductInterface;
use App\Contracts\Policies\ProceedProductInterface;
use App\Contracts\Policies\EffectProductInterface;

use App\Entities\Product;

interface DeleteProductInterface
{
	public function __construct(ValidatingProductInterface $pre, ProceedProductInterface $pro, EffectProductInterface $post);
	public function getError();
	public function getData();
	public function delete(Product $product);
}