<?php

namespace App\Contracts;

use App\Contracts\Policies\ValidatingShipmentInterface;
use App\Contracts\Policies\ProceedShipmentInterface;
use App\Contracts\Policies\EffectShipmentInterface;

interface ShippingOrderInterface
{
	public function __construct(ValidatingShipmentInterface $pre, ProceedShipmentInterface $pro, EffectShipmentInterface $post);
	public function getError();
	public function getData();
	public function fill(array $sale);
	public function save();
}