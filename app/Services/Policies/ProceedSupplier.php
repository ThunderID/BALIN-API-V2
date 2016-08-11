<?php

namespace App\Services\Policies;

use App\Contracts\Policies\ProceedSupplierInterface;

use App\Entities\Supplier;
use App\Entities\Image;

use Illuminate\Support\MessageBag;

class ProceedSupplier implements ProceedSupplierInterface
{
	public $errors;
	
	public $supplier;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 		= new MessageBag;
	}

	public function storesupplier(array $supplier)
	{
		$stored_supplier					= Supplier::findornew($supplier['id']);
		
		$stored_supplier->fill($supplier);

		if(!$stored_supplier->save())
		{
			$this->errors->add('Supplier', $stored_supplier->getError());
		}

		$this->supplier 					= $stored_supplier;
	}


	public function storeimage(Supplier $supplier, array $image)
	{
		$ids 							= [];
		$new_ids 						= [];

		foreach ($supplier->images as $key => $value) 
		{
			$ids[]						= $value['id'];
		}

		 $countimage					= Image::where('imageable_id', $supplier->id)
											->wherein('imageable_type', ['App\Models\Supplier', 'App\Entities\Supplier'])
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
				$images					= Image::where('imageable_id', $supplier->id)
											->wherein('imageable_type', ['App\Models\Supplier', 'App\Entities\Supplier'])
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
						$this->errors->add('Supplier', $image_mirror->getError());
					}
				}
			}

			$stored_image				= Image::findornew($value['id']);
			
			$stored_image 				= $stored_image->fill($value);

			$stored_image->imageable_id		= $supplier->id;
			$stored_image->imageable_type	= get_class($supplier);

			if(!$stored_image->save())
			{
				$this->errors->add('Supplier', $stored_image->getError());
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
					$this->errors->add('Supplier', $image_data->getError());
				}
			}
		}
	}

	public function deletesupplier(Supplier $supplier)
	{
		if(!$supplier->delete())
		{
			$this->errors->add('Supplier', $supplier->getError());
		}
	}

	public function deleteimage(Supplier $supplier)
	{
		foreach ($supplier->images as $key => $value) 
		{
			if(!$value->delete())
			{
				$this->errors->add('Supplier', $value->getError());
			}
		}
	}
}

