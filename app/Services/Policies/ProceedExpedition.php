<?php

namespace App\Services\Policies;

use App\Entities\Courier;
use App\Entities\ShippingCost;
use App\Entities\Image;

use Illuminate\Support\MessageBag;

use App\Contracts\Policies\ProceedExpeditionInterface;

class ProceedExpedition implements ProceedExpeditionInterface
{
	public $errors;

	public $courier;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 		= new MessageBag;
	}

	public function storecourier(array $courier)
	{
		$stored_courier		= Courier::findornew($courier['id']);
		
		$stored_courier->fill($courier);

		if(!$stored_courier->save())
		{
			$this->errors->add('Expedition', $stored_courier->getError());
		}

		$this->courier		= $stored_courier;
	}

	public function storeshippingcost(Courier $courier, array $shippingcosts)
	{
		$ids 							= [];
		$new_ids 						= [];

		foreach ($courier->shippingcosts as $key => $value) 
		{
			$ids[]						= $value['id'];
		}

		foreach ($shippingcosts as $key => $value) 
		{
			$stored_cost				= ShippingCost::findornew($value['id']);
			
			$stored_cost->fill($value);

			if(!$stored_cost->save())
			{
				$this->errors->add('Expedition', $stored_cost->getError());
			}
			else
			{
				$new_ids[]				= $stored_cost['id'];
			}
		}

		$difference_cost_ids			= array_diff($ids, $new_ids);

		if($difference_cost_ids)
		{
			foreach ($difference_cost_ids as $key => $value) 
			{
				$cost_data				= ShippingCost::find($value);

				if(!$cost_data->delete())
				{
					$this->errors->add('Expedition', $cost_data->getError());
				}
			}
		}
	}

	public function storeimage(Courier $courier, array $image)
	{
		$ids 							= [];
		$new_ids 						= [];

		foreach ($courier->images as $key => $value) 
		{
			$ids[]						= $value['id'];
		}

		 $countimage					= Image::where('imageable_id', $courier->id)
											->wherein('imageable_type', ['App\Models\Courier', 'App\Entities\Courier'])
											->where('is_default', 1)
											->count();
		if($countimage == 0)
		{
			$image[0]['is_default']		= 1;
		}

		foreach ($image as $key => $value) 
		{
			if($value['is_default'] == true)
			{
				$images					= Image::where('imageable_id', $courier->id)
											->wherein('imageable_type', ['App\Models\Courier', 'App\Entities\Courier'])
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
						$this->errors->add('Courier', $image_mirror->getError());
					}
				}
			}

			$stored_image				= Image::findornew($value['id']);
			
			$stored_image->fill($value);

			if(!$stored_image->save())
			{
				$this->errors->add('Courier', $stored_image->getError());
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
					$this->errors->add('Courier', $image_data->getError());
				}
			}
		}
	}

	public function deletecourier(Courier $courier)
	{
		if(!$courier->delete())
		{
			$this->errors->add('Expedition', $courier->getError());
		}
	}

	public function deleteshippingcost(Courier $courier)
	{
		foreach ($courier->shippingcosts as $key => $value) 
		{
			if(!$value->delete())
			{
				$this->errors->add('Expedition', $value->getError());
			}
		}
		
	}

	public function deleteimage(Courier $courier)
	{
		foreach ($courier->images as $key => $value) 
		{
			if(!$value->delete())
			{
				$this->errors->add('Expedition', $value->getError());
			}
		}
	}
}
