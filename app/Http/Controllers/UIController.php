<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Events\ProductSearched;

use \GenTux\Jwt\GetsJwtToken;

/**
 * Handle Protected Resource of customer
 * 
 * @author cmooy
 */
class UIController extends Controller
{
	use GetsJwtToken;
	
	public function __construct(Request $request)
	{
		$this->request 				= $request;
	}
	
	/**
	 * Display all sellable products
	 *
	 * @return Response
	 */
	public function products()
	{
        $payload                    = $this->jwtPayload();

		$result                     = new \App\Entities\Product;

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'recommended':
						$result     = $result->stats($value);
						break;
					case 'name':
						$result     = $result->name($value);
						break;
					case 'slug':
						$result     = $result->slug($value);

						if(!is_null($payload['context']['id']))
						{
							$data['user_id']    = $payload['context']['id'];
						}
						$data['slug']           = $value;
						$data['type']           = 'product';
						break;
					case 'categories':
						$result     = $result->categoriesslug($value);
						if(!is_null($payload['context']['id']))
						{
							$data['user_id']    = $payload['context']['id'];
						}
						$data['slug']           = $value;
						$data['type']           = 'category';
						break;
					case 'tags':
						$result     = $result->tagsslugorversion($value);

						if(!is_null($payload['context']['id']))
						{
							$data['user_id']    = $payload['context']['id'];
						}
						$data['slug']           = $value;
						$data['type']           = 'tag';
						break;
					case 'notid':
						$result     = $result->notid($value);
						break;
					default:
						# code...
						break;
				}

				if(isset($data))
				{
					event(new ProductSearched($data));
					unset($data);
				}
			}
		}

		$result                     = $result->sellable(true);

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
					case 'newest':
						$result     = $result->orderby('created_at', $value);
						break;
					
					default:
						# code...
						break;
				}
			}
		}

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

		$result                     = $result->with(['varians', 'images', 'labels', 'categories'])->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Display all clusters
	 *
	 * @return Response
	 */
	public function clusters($type = null)
	{
		if($type=='category')
		{
			$result                 = \App\Entities\Category::orderby('path', 'asc')->with(['category']);
		}
		else
		{
			$result                 = \App\Entities\Tag::orderby('path', 'asc')->with(['tag']);
		}

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'name':
						$result     = $result->name($value);
						break;
					
					default:
						# code...
						break;
				}
			}
		}

		$count                      = $result->count();

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

		$result                     = $result->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Display all labels
	 *
	 * @return Response
	 */
	public function labels($type = null)
	{
		$result                 = \App\Entities\ProductLabel::selectraw('lable as label')->groupby('label');

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'name' :
						$result     = $result->name($value);
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
					case 'label':
						$result     = $result->orderby('lable', $value);
						break;
				}
			}
		}

		$count                      = count($result->get(['lable']));

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

		$result                     = $result->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Display all configs
	 *
	 * @return Response
	 */
	public function config($type = null)
	{
		$sliders						= new \App\Entities\Slider;
		$sliders 						= $sliders->ondate(['now', 'now'])->with(['image'])->get()->toArray();
		$banners						= new \App\Entities\Banner;
		$banners 						= $banners->ondate(['now', 'now'])->with(['image'])->get()->toArray();
		$storeinfo						= new \App\Entities\Store;
		$storepage						= new \App\Entities\StorePage;
		$storepolicy					= new \App\Entities\Policy;
		$storeinfo 						= $storeinfo->default(true)->get()->toArray();
		$storepage 						= $storepage->default(true)->get()->toArray();
		$storepolicy 					= $storepolicy->default(true)->type('expired_cart')->first()->toArray();

		$store['banners']				= $banners;
		$store['sliders']				= $sliders;
		$store['info']					= $storeinfo;

		foreach ($storepage as $key => $value) 
		{
			$store[$value['type']]		= $value;
		}

		$store['info']['expired_cart']	= $storepolicy;

		return response()->json( JSend::success($store)->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Display all couriers
	 *
	 * @return Response
	 */
	public function couriers($type = null)
	{
		$result                 = new \App\Entities\Courier;

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'name' :
						$result     = $result->name($value);
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

		$result                     = $result->with(['shippingcosts'])->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Display all extensions
	 *
	 * @return Response
	 */
	public function extensions($type = null)
	{
		$result                 = \App\Entities\ProductExtension::active(true);

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'name' :
						$result     = $result->name($value);
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
				}
			}
		}

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

		$result                     = $result->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}
}
