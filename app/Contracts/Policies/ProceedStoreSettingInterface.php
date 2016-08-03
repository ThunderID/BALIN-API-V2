<?php

namespace App\Contracts\Policies;

interface ProceedStoreSettingInterface
{
	public function storestore(array $store);

	public function storestorepage(array $storepage);
	
	public function storeslider(array $slider);

	public function storepolicy(array $policy);
}
