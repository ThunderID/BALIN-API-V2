<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MyProductController extends Controller
{
	public function __construct(Request $request)
	{
		$this->request 				= $request;
	}
	
	/**
	 * Display recommended product by customer
	 *
	 * 1. Product has viewed/purchased category/tag
	 * 2. Product has size of purchased item
	 * @return Response
	 */
	public function recommended($user_id = 0)
	{
		//1. Check tag/category viewed
		$stat                       = \App\Entities\StatUserView::userid($user_id)->statabletype(['App\Entities\Category', 'App\Entities\Tag'])->get(['statable_id'])->toArray();

		//1b. Get slugs
		$slugs                      = [];
		$purchased_prods            = [];
		$purchased_varians          = [];
		foreach ($stat as $key => $value) 
		{
			$slugs[]                = \App\Entities\Cluster::find($value['statable_id'])['slug'];
		}

		$purchased                  = \App\Entities\TransactionDetail::TransactionSellOn(['paid', 'packed', 'shipping', 'delivered'])->where('transactions.user_id',$user_id)->groupby('varian_id')->with(['varian', 'varian.product', 'varian.product.clusters'])->get()->toArray();

		foreach ($purchased as $key => $value) 
		{
			//2. Check tag/category purchased
			foreach ($value['varian']['product']['clusters'] as $key2 => $value2) 
			{
				$slugs[]            = $value2['slug'];
			}

			$purchased_prods[]      = $value['varian']['product_id']; 
			$purchased_varians[]    = $value['varian']['size']; 
		}

		//2a. get slug of category/tag
		//2b. get product id
		//2c. get varian size
		$slug                       = array_unique($slugs);
		$productids                 = array_unique($purchased_prods);
		$variansize                 = array_unique($purchased_varians);

		$result                     = \App\Entities\Product::sellable(true);
		if(!empty($slug))
		{
			$result                 = $result->clustersslug($slug);
		}
		if(!empty($productids))
		{
			$result                 = $result->notid($productids);
		}
		if(!empty($variansize))
		{
			$result                 = $result->variansize($variansize);
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
	 * Display purchased product by customer
	 *
	 * @return Response
	 */
	public function purchased($user_id = 0)
	{
		$purchased_prods            = [];

		//1. get purchased item
		$purchased                  = \App\Entities\TransactionDetail::TransactionSellOn(['paid', 'packed', 'shipping', 'delivered'])->where('transactions.user_id',$user_id)->groupby('varian_id')->with(['varian'])->get()->toArray();

		foreach ($purchased as $key => $value) 
		{
			//2. get product id
			$purchased_prods[]      = $value['varian']['product_id']; 
		}

		$productids                 = array_unique($purchased_prods);

		$result                     = \App\Entities\Product::id($productids)->sellable(true);

		$count                      = count($result->get(['id']));

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
	 * Display viewed product by customer
	 *
	 * @return Response
	 */
	public function viewed($user_id = 0)
	{
		//1. Check product viewed
		$stat                       = \App\Entities\StatUserView::userid($user_id)->statabletype('App\Entities\Product')->get(['statable_id'])->toArray();

		//1b. Get ids
		$viewed_prods               = [];

		foreach ($stat as $key => $value) 
		{
			$viewed_prods[]         = $value['statable_id'];
		}

		$productids                 = array_unique($viewed_prods);

		$result                     = \App\Entities\Product::id($productids)->sellable(true);

		$count                      = count($result->get(['id']));

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
}
