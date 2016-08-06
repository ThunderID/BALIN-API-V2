<?php

namespace App\Contracts\Policies;

use App\Entities\StoreSetting;

interface ProceedStoreSettingInterface
{
	public function storestore(array $store);

	public function storestorepage(array $storepage);
	
	public function storeslider(array $slider);

	public function storepolicy(array $policy);
	
	public function storebanner(array $banner);

	public function storeimage(StoreSetting $storesetting, array $image);
}
