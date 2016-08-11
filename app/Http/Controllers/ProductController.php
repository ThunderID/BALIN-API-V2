<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Services\BalinStoreProduct;
use App\Services\BalinDeleteProduct;

/**
 * Handle Protected Resource of product
 * 
 * @author cmooy
 */
class ProductController extends Controller
{
	public function __construct(Request $request, BalinStoreProduct $store_product, BalinDeleteProduct $delete_product)
	{
		$this->request 				= $request;
		$this->store_product		= $store_product;
		$this->delete_product		= $delete_product;
	}
	
	/**
	 * Display all products
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index()
	{
		$result                     = new \App\Entities\Product;

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
					return response()->json( JSend::error([$key.' harus bernilai asc atau desc.'])->asArray());
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

		$result                     = $result->with(['varians'])->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
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
		$result                     = \App\Entities\Product::id($id)->with(['varians', 'categories', 'tags', 'labels', 'images', 'prices'])->first();

		if($result)
		{
			return response()->json( JSend::success($result->toArray())->asArray())
					->setCallback($this->request->input('callback'));
		}
		
		return response()->json( JSend::error(['ID Tidak Valid.'])->asArray());
	}

	/**
	 * Store a product
	 *
	 * @return Response
	 */
	public function store()
	{
		if(!Input::has('product'))
		{
			return response()->json( JSend::error(['Tidak ada data produk.'])->asArray());
		}

		$product                    = Input::get('product');

		$this->store_product->fill($product);

		if(!$this->store_product->save())
		{
			return response()->json( JSend::error($this->store_product->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($this->store_product->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
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
		$product                    = \App\Entities\Product::id($id)->with(['varians', 'categories', 'tags', 'labels', 'images', 'prices'])->first();

		if(!$product)
		{
			return response()->json( JSend::error(['Produk tidak ditemukan.'])->asArray());
		}

		if($this->delete_product->delete($product))
		{
			return response()->json( JSend::success($this->delete_product->getData())->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::error($this->delete_product->getError())->asArray());
	}
}
