<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Handle Protected Resource of product
 * 
 * @author cmooy
 */
class ProductController extends Controller
{
	/**
	 * Display all products
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index()
	{
		$result                     = new \App\Models\Product;

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'labelname':
						$result     = $result->labelsname($value);
						break;
					case 'name':
						$result     = $result->name($value);
						break;
					case 'slug':
						$result     = $result->slug($value);
						break;
					case 'discount':
						$result     = $result->discount($value);
						break;
					case 'categories':
						$result     = $result->categoriesslug($value);
						break;
					case 'tags':
						$result     = $result->tagsslug($value);
						break;
					default:
						# code...
						break;
				}
			}
		}

		if(Input::has('sort'))
		{
			$sort                 = Input::get('sort');

			foreach ($sort as $key => $value) 
			{
				if(!in_array($value, ['asc', 'desc']))
				{
					return new JSend('error', (array)Input::all(), $key.' harus bernilai asc atau desc.');
				}
				switch (strtolower($key)) 
				{
					case 'name':
						$result     = $result->orderby($key, $value);
						break;
					case 'price':
						$result     = $result->orderby($key, $value);
						break;
					case 'discount':
						$result     = $result->orderby('IFNULL(IF(prices.promo_price=0, 0, SUM(prices.price - prices.promo_price)), 0)', $value);
						break;
					case 'promo':
						$result     = $result->orderby('promo_price', $value);
						break;
					case 'newest':
						$result     = $result->orderby('created_at', $value);
						break;
					case 'stock':
						$result     = $result->orderby('current_stock', $value);
						break;
					default:
						# code...
						break;
				}
			}
		}

		$count                      = count($result->get());

		if(Input::has('skip'))
		{
			$skip                   = Input::get('skip');
			$result                 = $result->skip($skip);
		}

		if(Input::has('take'))
		{
			$take                   = Input::get('take');
			$result                 = $result->take($take);
		}

		$result                     = $result->with(['varians'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display a product
	 *
	 * @param product id
	 * @return Response
	 */
	public function detail($id = null)
	{
		//
		$result                     = \App\Models\Product::id($id)->with(['varians', 'categories', 'tags', 'labels', 'images', 'prices'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}
		
		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * Store a product
	 *
	 * 1. Save Product
	 * 2. Save Varian
	 * 3. Save Price
	 * 4. Save Category
	 * 5. Save Tag
	 * 6. Save Label
	 * 7. Save Image
	 * 
	 * @return Response
	 */
	public function store()
	{
		if(!Input::has('product'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data produk.');
		}

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Validate Product Parameter
		$product                    = Input::get('product');

		//1a. Get original data
		if(is_null($product['id']))
		{
			$product_data           = new \App\Models\Product;
			$is_new                 = true;
		}
		else
		{
			$product_data           = \App\Models\Product::findornew($product['id']);
			$is_new                 = false;
		}

		if(isset($product['description']))
		{
			$product['description'] = json_decode($product['description'], true);
		}

		$product_rules              =   [
											'name'                      => 'required|max:255',
											'upc'                       => 'required|max:255|unique:products,upc,'.(!is_null($product['id']) ? $product['id'] : ''),
											'slug'                      => 'max:255|unique:products,slug,'.(!is_null($product['id']) ? $product['id'] : ''),
											'description.description'   => 'max:512',
											'description.fit'           => 'max:512',
										];

		//1b. Validate Basic Product Parameter
		$validator                  = Validator::make($product, $product_rules);

		if (!$validator->passes())
		{
			$errors->add('Product', $validator->errors());
		}
		else
		{
			//if validator passed, save product
			if(isset($product['description']))
			{
				$product['description'] = json_encode($product['description']);
			}
			else
			{
				$product['description'] = json_encode(['description' => '', 'fit' => '']);
			}

			$product_data           = $product_data->fill($product);

			if(!$product_data->save())
			{
				$errors->add('Product', $product_data->getError());
			}
		}
		//End of validate product

		//2. Validate Product Varian Parameter
		if(!$errors->count() && isset($product['varians']) && is_array($product['varians']))
		{
			$varian_current_ids         = [];
			foreach ($product['varians'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$varian_data		= \App\Models\Varian::findornew($value['id']);

					$varian_rules		=   [
												'product_id'	=> 'exists:products,id|'.($is_new ? '' : 'in:'.$product_data['id']),
												'sku'			=> 'required|max:255|unique:varians,sku,'.(!is_null($value['id']) ? $value['id'] : ''),
												'size'			=> 'required|max:255',
											];

					$validator      = Validator::make($value, $varian_rules);

					//if there was varian and validator false
					if (!$validator->passes())
					{
						$errors->add('Varian', $validator->errors);
					}
					else
					{
						$value['product_id']            = $product_data['id'];

						$varian_data                    = $varian_data->fill($value);

						if(!$varian_data->save())
						{
							$errors->add('Varian', $varian_data->getError());
						}
						else
						{
							$varian_current_ids[]       = $varian_data['id'];
						}
					}
				}
			}

			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$varians                            = \App\Models\Varian::productid($product['id'])->get(['id'])->toArray();
				
				$varian_should_be_ids               = [];
				foreach ($varians as $key => $value) 
				{
					$varian_should_be_ids[]         = $value['id'];
				}

				$difference_varian_ids              = array_diff($varian_should_be_ids, $varian_current_ids);

				if($difference_varian_ids)
				{
					foreach ($difference_varian_ids as $key => $value) 
					{
						$varian_data                = \App\Models\Varian::find($value);

						if(!$varian_data->delete())
						{
							$errors->add('Varian', $varian_data->getError());
						}
					}
				}
			}
		}

		//End of validate product varian

		//3. Validate Product Price Parameter
		if(!$errors->count() && isset($product['prices']) && is_array($product['prices']))
		{
			$price_current_ids         = [];
			foreach ($product['prices'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$price_data        	= \App\Models\Price::findornew($value['id']);

					$price_rules		=   [
												'product_id'	=> 'exists:products,id|'.($is_new ? '' : 'in:'.$product_data['id']),
												'price'			=> 'required|numeric',
												'promo_price'	=> 'numeric|max:price',
												'started_at'	=> 'required|date_format:"Y-m-d H:i:s"',
											];

					$validator			= Validator::make($value, $price_rules);

					//if there was price and validator false
					if (!$validator->passes())
					{
						$errors->add('Price', $validator->errors());
					}
					else
					{
						$value['product_id']           = $product_data['id'];

						$price_data                    = $price_data->fill($value);

						if(!$price_data->save())
						{
							$errors->add('Price', $price_data->getError());
						}
						else
						{
							$price_current_ids[]       = $price_data['id'];
						}
					}
				}
			}

			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$prices                            = \App\Models\Price::productid($product['id'])->get(['id'])->toArray();
				
				$price_should_be_ids               = [];
				foreach ($prices as $key => $value) 
				{
					$price_should_be_ids[]         = $value['id'];
				}

				$difference_price_ids              = array_diff($price_should_be_ids, $price_current_ids);

				if($difference_price_ids)
				{
					foreach ($difference_price_ids as $key => $value) 
					{
						$price_data                = \App\Models\Price::find($value);

						if(!$price_data->delete())
						{
							$errors->add('Price', $price_data->getError());
						}
					}
				}
			}
		}
		//End of validate product price
		
		$cluster_current_ids                    = [];

		//4. Validate Product Category Parameter
		if(!$errors->count() && isset($product['categories']) && is_array($product['categories']))
		{
			$category_current_ids               = [];

			foreach ($product['categories'] as $key => $value) 
			{
				$category                       = \App\Models\Category::find($value['id']);

				if($category)
				{
					$category_current_ids[]     = $value['id'];
					$cluster_current_ids[]      = $value['id'];
				}
				elseif(isset($value['name']))
				{
					$category_data              = new \App\Models\Category;
					$category_data              = $category_data->fill(['name' => $value['name']]);

					if(!$category_data->save())
					{
						$errors->add('Category', $category_data->getError());
					}
					else
					{
						$category_current_ids[]     = $category_data['id'];
						$cluster_current_ids[]      = $category_data['id'];
					}
				}
			}

			// if(!$errors->count())
			// {
			//     if(!$product_data->categories()->sync($category_current_ids))
			//     {
			//         $errors->add('Category', 'Kategori produk tidak tersimpan.');
			//     }
			// }
		}
		//End of validate product category

		//5. Validate Product Tag Parameter
		if(!$errors->count() && isset($product['tags']) && is_array($product['tags']))
		{
			$tag_current_ids                = [];

			foreach ($product['tags'] as $key => $value) 
			{
				$tag                        = \App\Models\Tag::find($value['id']);

				if($tag)
				{
					$tag_current_ids[]      = $value['id'];
					$cluster_current_ids[]      = $value['id'];
				}
				elseif(isset($value['name']))
				{
					$tag_data               = new \App\Models\Tag;
					$tag_data               = $tag_data->fill(['name' => $value['name']]);

					if(!$tag_data->save())
					{
						$errors->add('Tag', $tag_data->getError());
					}
					else
					{
						$tag_current_ids[]          = $tag_data['id'];
						$cluster_current_ids[]      = $tag_data['id'];
					}
				}
			}

			// if(!$errors->count())
			// {
			//     if(!$product_data->tags()->sync($tag_current_ids))
			//     {
			//         $errors->add('Tag', 'Tag produk tidak tersimpan.');
			//     }
			// }
		}

		if(!$errors->count() && isset($cluster_current_ids))
		{
			if(!$product_data->clusters()->sync($cluster_current_ids))
			{
				$errors->add('Product', 'Tag/Kategori produk tidak tersimpan.');
			}
		}

		//End of validate product category

		//6. Validate Product Label Parameter
		if(!$errors->count() && isset($product['labels']) && is_array($product['labels']))
		{
			$label_current_ids         = [];
			foreach ($product['labels'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$label_data			= \App\Models\ProductLabel::findornew($value['id']);

					$label_rules   		=   [
												'product_id'	=> 'exists:products,id|'.($is_new ? '' : 'in:'.$product_data['id']),
												'label'			=> 'max:255',
												'started_at'	=> 'required|date_format:"Y-m-d H:i:s"',
												'ended_at'		=> 'date_format:"Y-m-d H:i:s"',
											];

					if(!isset($value['ended_at']) || !strtotime($value['ended_at']))
					{
						unset($value['ended_at']);
					}

					$validator      = Validator::make($value, $label_rules);

					//if there was label and validator false
					if (!$validator->passes())
					{
						$errors->add('ProductLabel', $validator->errors());
					}
					else
					{
						$value['product_id']			= $product_data['id'];
						if(isset($value['label']))
						{
							$value['lable']				= $value['label'];
						}

						$label_data                    = $label_data->fill($value);

						if(!$label_data->save())
						{
							$errors->add('ProductLabel', $label_data->getError());
						}
						else
						{
							$label_current_ids[]       = $label_data['id'];
						}
					}
				}
			}
			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$labels                            = \App\Models\ProductLabel::productid($product['id'])->get(['id'])->toArray();
				
				$label_should_be_ids               = [];
				foreach ($labels as $key => $value) 
				{
					$label_should_be_ids[]         = $value['id'];
				}

				$difference_label_ids              = array_diff($label_should_be_ids, $label_current_ids);

				if($difference_label_ids)
				{
					foreach ($difference_label_ids as $key => $value) 
					{
						$label_data                = \App\Models\ProductLabel::find($value);

						if(!$label_data->delete())
						{
							$errors->add('ProductLabel', $label_data->getError());
						}
					}
				}
			}
		}
		//End of validate product label

		//7. Validate Product Image Parameter
		if(!$errors->count() && isset($product['images']) && is_array($product['images']))
		{
			$image_current_ids         = [];
			foreach ($product['images'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$image_data        = \App\Models\Image::findornew($value['id']);

					$image_rules   	=   [
											'imageable_id'              => 'exists:products,id|'.($is_new ? '' : 'in:'.$product_data['id']),
											'imageable_type'			=> ($is_new ? '' : 'in:'.get_class($product_data)),
											'thumbnail'                 => 'required|max:255',
											'image_xs'                  => 'required|max:255',
											'image_sm'                  => 'required|max:255',
											'image_md'                  => 'required|max:255',
											'image_lg'                  => 'required|max:255',
											'is_default'                => 'boolean',
										];

					$validator      	= Validator::make($value, $image_rules);

					//if there was image and validator false
					if (!$validator->passes())
					{
						$errors->add('Image', $validator->errors());
					}
					else
					{
						$value['imageable_id']          = $product_data['id'];
						$value['imageable_type']        = get_class($product_data);

						$image_data                     = $image_data->fill($value);

						if(!$image_data->save())
						{
							$errors->add('Image', $image_data->getError());
						}
						else
						{
							$image_current_ids[]       = $image_data['id'];
						}
					}
				}
			}
			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$images                            = \App\Models\Image::imageableid($product['id'])->imageabletype(get_class($product_data))->get(['id'])->toArray();
				
				$image_should_be_ids               = [];
				foreach ($images as $key => $value) 
				{
					$image_should_be_ids[]         = $value['id'];
				}

				$difference_image_ids              = array_diff($image_should_be_ids, $image_current_ids);

				if($difference_image_ids)
				{
					foreach ($difference_image_ids as $key => $value) 
					{
						$image_data                = \App\Models\Image::find($value);

						if(!$image_data->delete())
						{
							$errors->add('Image', $image_data->getError());
						}
					}
				}
			}
		}
		//End of validate product image

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_product              = \App\Models\Product::id($product_data['id'])->with(['varians', 'categories', 'tags', 'labels', 'images', 'prices'])->first()->toArray();

		return new JSend('success', (array)$final_product);
	}

	/**
	 * Delete a product
	 *
	 * @param product id
	 * @return Response
	 */
	public function delete($id = null)
	{
		//
		$product                    = \App\Models\Product::id($id)->with(['varians', 'categories', 'tags', 'labels', 'images', 'prices'])->first();

		if(!$product)
		{
			return new JSend('error', (array)Input::all(), 'Produk tidak ditemukan.');
		}

		$result                     = $product->toArray();

		if($product->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $product->getError());
	}
}
