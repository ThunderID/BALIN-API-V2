<?php

namespace App\Services\Policies;

use App\Entities\StoreSetting;
use App\Entities\Store;
use App\Entities\StorePage;
use App\Entities\Slider;
use App\Entities\Policy;
use App\Entities\Banner;
use App\Entities\Image;

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

		if(isset($slider['images']))
		{
			$this->storeimage($stored_store, $slider['images']);
		}
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

	public function storebanner(array $banner)
	{
		$stored_store					= Banner::findornew($banner['id']);

		$stored_store->fill($banner);

		if(!$stored_store->save())
		{
			$this->errors->add('Store', $stored_store->getError());
		}

		$this->storesetting 			= $stored_store;

		if(isset($banner['images']))
		{
			$this->storeimage($stored_store, $banner['images']);
		}
	}


	public function storeimage(StoreSetting $storesetting, array $image)
	{
		$ids 							= [];
		$new_ids 						= [];

		foreach ($storesetting->images as $key => $value) 
		{
			$ids[]						= $value['id'];
		}

		$countimage						= Image::where('imageable_id', $storesetting->id)
											->wherein('imageable_type', ['App\Models\Slider', 'App\Entities\Slider', 'App\Entities\Banner', 'App\Models\StoreSetting'])
											->where('is_default', 1)
											->count();
		if($countimage == 0)
		{
			$image[0]['is_default']		= 1;
		}

		foreach ($image as $key => $value) 
		{
			if(isset($value['is_default']) && $value['is_default'] == true)
			{
				$images					= Image::where('imageable_id', $storesetting->id)
											->wherein('imageable_type', ['App\Models\Slider', 'App\Entities\Slider', 'App\Entities\Banner', 'App\Models\StoreSetting'])
											->where('is_default', 1)
											->where('id','!=', $value['id'])
											->get();

				foreach ($images as $image_mirror) 
				{
					//1a. set is_default to false for other image_mirror
					$image_mirror->fill([
						'is_default'		=> 0,
					]);

					if(!$image_mirror->save())
					{
						$this->errors->add('Store', $image_mirror->getError());
					}
				}
			}

			$stored_image				= Image::findornew($value['id']);
			
			$stored_image->fill($value);
			$stored_image->imageable_id		= $storesetting->id;
			$stored_image->imageable_type	= get_class($storesetting);

			if(!$stored_image->save())
			{
				$this->errors->add('Store', $stored_image->getError());
			}
			else
			{
				$new_ids[]				= $stored_image['id'];
			}
		}

		$difference_image_ids			= array_diff($ids, $new_ids);

		if($difference_image_ids)
		{
			foreach ($difference_image_ids as $key => $value) 
			{
				$image_data				= Image::find($value);

				if(!$image_data->delete())
				{
					$this->errors->add('Store', $image_data->getError());
				}
			}
		}
	}

}

