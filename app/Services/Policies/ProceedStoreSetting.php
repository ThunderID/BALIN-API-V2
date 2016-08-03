<?php

namespace App\Services\Policies;

use App\Entities\Store;
use App\Entities\StorePage;
use App\Entities\Slider;
use App\Entities\Policy;

use App\Contracts\Policies\ProceedStoreSettingInterface;

use Illuminate\Support\MessageBag;

class ProceedStoreSetting implements ProceedStoreSettingInterface
{
	public $errors;

	public $storesetting;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 		= new MessageBag;
	}

	public function storestore(array $store)
	{
		$stored_store					= Store::findornew($store['id']);
		
		$stored_store->fill($store);

		if(!$stored_store->save())
		{
			$this->errors->add('Store', $stored_store->getError());
		}

		$this->storesetting 			= $stored_store;
	}

	public function storestorepage(array $storepage)
	{
		$stored_store					= StorePage::findornew($storepage['id']);
		
		$stored_store->fill($storepage);

		if(!$stored_store->save())
		{
			$this->errors->add('Store', $stored_store->getError());
		}

		$this->storesetting 			= $stored_store;
	}

	public function storeslider(array $slider)
	{
		$stored_store					= Slider::findornew($slider['id']);
		
		$stored_store->fill($slider);

		if(!$stored_store->save())
		{
			$this->errors->add('Store', $stored_store->getError());
		}

		$this->storesetting 			= $stored_store;
	}

	public function storepolicy(array $policy)
	{
		$stored_store					= Policy::findornew($policy['id']);
		
		$stored_store->fill($policy);

		if(!$stored_store->save())
		{
			$this->errors->add('Store', $stored_store->getError());
		}

		$this->storesetting 			= $stored_store;
	}
}

