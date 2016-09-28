<?php

namespace App\Services\Policies;

use App\Entities\Product;
use App\Entities\Varian;
use App\Entities\Price;
use App\Entities\ProductLabel;
use App\Entities\Image;

use App\Contracts\Policies\ProceedProductInterface;

use Carbon\Carbon;

use Illuminate\Support\MessageBag;

class ProceedProduct implements ProceedProductInterface
{
	public $errors;

	public $product;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct()
	{
		$this->errors 	= new MessageBag;
	}

	public function storeproduct(array $product)
	{
		$stored_product					= Product::findornew($product['id']);
		
		$stored_product->fill($product);

		if(!$stored_product->save())
		{
			$this->errors->add('Product', $stored_product->getError());
		}

		$this->product 					= $stored_product;
	}

	public function storevarian(Product $product, array $varian)
	{
		$ids 							= [];
		$new_ids 						= [];

		foreach ($product->varians as $key => $value) 
		{
			$ids[]						= $value['id'];
		}

		foreach ($varian as $key => $value) 
		{

			$stored_varian				= Varian::findornew($value['id']);
			
			$stored_varian->fill($value);
			$stored_varian->product_id 	= $product->id;

			if(!$stored_varian->save())
			{
				$this->errors->add('Product', $stored_varian->getError());
			}
			else
			{
				$new_ids[]				= $stored_varian['id'];
			}
		}

		$difference_varian_ids              = array_diff($ids, $new_ids);

		if($difference_varian_ids)
		{
			foreach ($difference_varian_ids as $key => $value) 
			{
				$varian_data				= Varian::find($value);

				if(!$varian_data->delete())
				{
					$this->errors->add('Product', $varian_data->getError());
				}
			}
		}
	}

	public function storeprice(Product $product, array $price)
	{
		$ids 							= [];
		$new_ids 						= [];

		foreach ($product->prices as $key => $value) 
		{
			$ids[]						= $value['id'];
		}

		foreach ($price as $key => $value) 
		{
			$stored_price				= Price::findornew($value['id']);
			
			$stored_price->fill($value);
			$stored_price->product_id 	= $product->id;

			if(!$stored_price->save())
			{
				$this->errors->add('Product', $stored_price->getError());
			}
			else
			{
				$new_ids[]				= $stored_price['id'];
			}
		}

		$difference_price_ids			= array_diff($ids, $new_ids);

		if($difference_price_ids)
		{
			foreach ($difference_price_ids as $key => $value) 
			{
				$price_data				= Price::find($value);

				if(!$price_data->delete())
				{
					$this->errors->add('Product', $price_data->getError());
				}
			}
		}
	}

	public function storelabel(Product $product, array $label)
	{
		$ids 							= [];
		$new_ids 						= [];

		foreach ($product->labels as $key => $value) 
		{
			$ids[]						= $value['id'];
		}

		foreach ($label as $key => $value) 
		{
			$stored_label				= ProductLabel::findornew($value['id']);
			$stored_label->product_id 	= $product->id;
			
			if($value['ended_at']=='-0001-11-30 00:00:00')
			{
				unset($value['ended_at']);
			}

			$stored_label->fill($value);

			if(!$stored_label->save())
			{
				$this->errors->add('Product', $stored_label->getError());
			}
			else
			{
				$new_ids[]				= $stored_label['id'];
			}
		}

		$difference_label_ids			= array_diff($ids, $new_ids);

		if($difference_label_ids)
		{
			foreach ($difference_label_ids as $key => $value) 
			{
				$label_data				= ProductLabel::find($value);

				if(!$label_data->delete())
				{
					$this->errors->add('Product', $label_data->getError());
				}
			}
		}
	}

	public function storecluster(Product $product, array $cluster)
	{
		foreach ($cluster as $key => $value) 
		{
			$cluster_current_ids[]		= $value['id'];
		}

		if(!$product->clusters()->sync($cluster_current_ids))
		{
			$this->errors->add('Product', 'Tag/Kategori produk tidak tersimpan.');
		}
	}

	public function storeimage(Product $product, array $image)
	{
		$ids 							= [];
		$new_ids 						= [];

		foreach ($product->images as $key => $value) 
		{
			$ids[]						= $value['id'];
		}

		 $countimage					= Image::where('imageable_id', $product->id)
											->wherein('imageable_type', ['App\Models\Product', 'App\Entities\Product'])
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
				$images					= Image::where('imageable_id', $product->id)
											->wherein('imageable_type', ['App\Models\Product', 'App\Entities\Product'])
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
						$this->errors->add('Product', $image_mirror->getError());
					}
				}
			}

			$stored_image				= Image::findornew($value['id']);
			
			$stored_image->fill($value);
			$stored_image->imageable_id		= $product->id;
			$stored_image->imageable_type	= get_class($product);

			if(!$stored_image->save())
			{
				$this->errors->add('Product', $stored_image->getError());
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
					$this->errors->add('Product', $image_data->getError());
				}
			}
		}
	}

	public function deleteproduct(Product $product)
	{
		if(!$product->delete())
		{
			$this->errors->add('Product', $product->getError());
		}
	}

	public function deletevarian(Product $product)
	{
		foreach ($product->varians as $key => $value) 
		{
			if(!$value->delete())
			{
				$this->errors->add('Product', $value->getError());
			}
		}
	}

	public function deleteprice(Product $product)
	{
		foreach ($product->prices as $key => $value) 
		{
			if(!$value->delete())
			{
				$this->errors->add('Product', $value->getError());
			}
		}
	}

	public function deletelabel(Product $product)
	{
		foreach ($product->labels as $key => $value) 
		{
			if(!$value->delete())
			{
				$this->errors->add('Product', $value->getError());
			}
		}
	}

	public function deletecluster(Product $product)
	{
		if(!$product->clusters()->sync([]))
		{
			$this->errors->add('Product', 'Tag/Kategori produk tidak tersimpan.');
		}
	}

	public function deleteimage(Product $product)
	{
		foreach ($product->images as $key => $value) 
		{
			if(!$value->delete())
			{
				$this->errors->add('Product', $value->getError());
			}
		}
	}
}

