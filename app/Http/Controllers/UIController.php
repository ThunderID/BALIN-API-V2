<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\ProductSearched;

/**
 * Handle Protected Resource of customer
 * 
 * @author cmooy
 */
class UIController extends Controller
{
	/**
	 * Display all sellable products
	 *
	 * @return Response
	 */
	public function products()
	{
		$user						= \LucaDegasperi\OAuth2Server\Facades\Authorizer::getResourceOwnerId();
		$user						= json_decode($user, true)['data'];

		$result                     = new \App\Models\Product;

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

						if($user)
						{
							$data['user_id']    = $user['id'];
						}
						$data['slug']           = $value;
						$data['type']           = 'product';
						break;
					case 'categories':
						$result     = $result->categoriesslug($value);
						if($user)
						{
							$data['user_id']    = $user['id'];
						}
						$data['slug']           = $value;
						$data['type']           = 'category';
						break;
					case 'tags':
						$result     = $result->tagsslug($value);

						if($user)
						{
							$data['user_id']    = $user['id'];
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

		$result                     = $result->with(['varians', 'images', 'labels'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
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
			$result                 = \App\Models\Category::orderby('path', 'asc')->with(['category']);
		}
		else
		{
			$result                 = \App\Models\Tag::orderby('path', 'asc')->with(['tag']);
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

		$result                     = $result->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display all labels
	 *
	 * @return Response
	 */
	public function labels($type = null)
	{
		$result                 = \App\Models\ProductLabel::selectraw('lable as label')->groupby('label');

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
					return new JSend('error', (array)Input::all(), $key.' harus bernilai asc atau desc.');
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

		$result                     = $result->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display all configs
	 *
	 * @return Response
	 */
	public function config($type = null)
	{
		$sliders						= \App\Models\Slider::ondate('now')->with(['image'])->get()->toArray();
		$storeinfo						= new \App\Models\Store;
		$storepage						= new \App\Models\StorePage;
		$storepolicy					= new \App\Models\Policy;
		$storeinfo 						= $storeinfo->default(true)->get()->toArray();
		$storepage 						= $storepage->default(true)->get()->toArray();
		$storepolicy 					= $storepolicy->default(true)->type('expired_paid')->first()->toArray();

		$store['sliders']				= $sliders;
		$store['info']					= $storeinfo;

		foreach ($storepage as $key => $value) 
		{
			$store[$value['type']]		= $value;
		}

		$store['info']['expired_paid']	= $storepolicy;

		return new JSend('success', (array)$store);
	}

	/**
	 * Display all extensions
	 *
	 * @return Response
	 */
	public function extensions($type = null)
	{
		$result                 = \App\Models\ProductExtension::active(true);

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
					return new JSend('error', (array)Input::all(), $key.' harus bernilai asc atau desc.');
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

		$result                     = $result->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}
}
