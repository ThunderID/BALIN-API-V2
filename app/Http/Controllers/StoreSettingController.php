<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Services\BalinStoreWebSliderSetting;
use App\Services\BalinStoreWebBannerSetting;
use App\Services\BalinStoreWebSetting;
use App\Services\BalinStoreWebPolicySetting;
use App\Services\BalinStoreWebPageSetting;

/**
 * Handle Protected display and store of setting, there were 4 type of setting, there are slider, page, store, and policy
 * 
 * @author cmooy
 */
class StoreSettingController extends Controller
{
	public function __construct(Request $request, BalinStoreWebSliderSetting $balinslider, BalinStoreWebBannerSetting $balinbanner, BalinStoreWebSetting $balinstore, BalinStoreWebPolicySetting $balinpolicy, BalinStoreWebPageSetting $balinpage)
	{
		$this->request 				= $request;
		$this->balinslider			= $balinslider;
		$this->balinbanner			= $balinbanner;
		$this->balinstore			= $balinstore;
		$this->balinpolicy			= $balinpolicy;
		$this->balinpage			= $balinpage;
	}

	/**
	 * Display all settings
	 *
	 * @param type, search, skip, take
	 * @return Response
	 */
	public function index($type = null)
	{
		$result                 = new \App\Entities\StoreSetting;

		switch (strtolower($type)) 
		{
			case 'slider':
				$result         = \App\Entities\Slider::with(['image']);
				break;
			case 'banner':
				$result         = \App\Entities\Banner::with(['image']);
				break;
			case 'page':
				$result         = new \App\Entities\StorePage;
				break;
			case 'store':
				$result         = new \App\Entities\Store;
				break;
			case 'policy':
				$result         = new \App\Entities\Policy;
				break;
		}

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'default':
						if(strtolower($type)!='slider')
						{
							$result = $result->default($value);
						}
						break;
					case 'ondate':
						$result = $result->ondate($value);
						break;
					case 'type':
						$result = $result->type($value);
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

		$result                     = $result->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Display a setting
	 *
	 * @return Response
	 */
	public function detail($id = null)
	{
		$result                 = \App\Entities\StoreSetting::id($id)->first();

		if($result)
		{
			if($result['type']=='slider')
			{
				$result         = \App\Entities\Slider::id($id)->with(['images'])->first();
			}
			elseif(str_is('banner*', $result['type']))
			{
				$result         = \App\Entities\Banner::id($id)->with(['images'])->first();
			}

			return response()->json( JSend::success($result->toArray())->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::fail(['ID Tidak Valid.']));
	}

	/**
	 * Store a setting
	 *
	 * @return Response
	 */
	public function store()
	{
		if(!Input::has('setting'))
		{
			return response()->json( JSend::error(['Tidak ada data setting.'])->asArray());
		}

		$errors                     = new MessageBag();

		//1. Validate StoreSetting Parameter
		$setting                    = Input::get('setting');
	
		switch ($setting['type']) 
		{
			case 'slider':
				$store_setting			= $this->balinslider;
				break;
			case 'banner':case 'banner_instagram' :
				$store_setting			= $this->balinbanner;
				break;
			case 'about_us':case 'why_join':case 'term_and_condition':
				$store_setting			= $this->balinpage;
				break;
			case 'url': case 'logo': case 'facebook_url': case 'twitter_url': case 'instagram_url': case 'email': case 'phone': case 'address': case 'bank_information' :
				$store_setting			= $this->balinstore;
				break;
			default:
				$store_setting			= $this->balinpolicy;
				break;
		}

		$store_setting->fill($setting);

		if(!$store_setting->save())
		{
			return response()->json( JSend::error($store_setting->getError()->toArray())->asArray());
		}

		return response()->json( JSend::success($store_setting->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}
}
