<?php

namespace App\Services\Policies;

use App\Entities\ProductExtension as Ornament;
use App\Entities\Image;

use Illuminate\Support\MessageBag;

use App\Contracts\Policies\ProceedOrnamentInterface;

class ProceedOrnament implements ProceedOrnamentInterface
{
	public $errors;

	public $ornament;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 		= new MessageBag;
	}

	public function storeornament(array $ornament)
	{
		$stored_ornament					= Ornament::findornew($ornament['id']);
		
		$stored_ornament->fill($ornament);

		if(!$stored_ornament->save())
		{
			$this->errors->add('Ornament', $stored_ornament->getError());
		}

		$this->ornament 					= $stored_ornament;
	}

	public function deleteornament(Ornament $ornament)
	{
		if(!$ornament->delete())
		{
			$this->errors->add('Ornament', $ornament->getError());
		}
	}

	public function storeimage(Ornament $ornament, array $image)
	{
		$ids 							= [];
		$new_ids 						= [];

		foreach ($ornament->images as $key => $value) 
		{
			$ids[]						= $value['id'];
		}

		 $countimage					= Image::where('imageable_id', $ornament->id)
											->wherein('imageable_type', ['App\Models\ProductExtension', 'App\Entities\ProductExtension'])
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
				$images					= Image::where('imageable_id', $ornament->id)
											->wherein('imageable_type', ['App\Models\ProductExtension', 'App\Entities\ProductExtension'])
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
						$this->errors->add('Ornament', $image_mirror->getError());
					}
				}
			}

			$stored_image					= Image::findornew($value['id']);
			
			$stored_image 					= $stored_image->fill($value);
			$stored_image->imageable_id		= $ornament->id;
			$stored_image->imageable_type	= get_class($ornament);

			if(!$stored_image->save())
			{
				$this->errors->add('Ornament', $stored_image->getError());
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
					$this->errors->add('Ornament', $image_data->getError());
				}
			}
		}
	}
}
