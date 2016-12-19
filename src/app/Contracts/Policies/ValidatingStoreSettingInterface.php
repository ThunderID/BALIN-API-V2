<?php

namespace App\Contracts\Policies;

interface ValidatingStoreSettingInterface
{
	public function validatestore(array $store);

	public function validatestorepage(array $storepage);
	
	public function validateslider(array $slider);

	public function validatepolicy(array $policy);

	public function validatebanner(array $banner);
}
