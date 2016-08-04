<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected display and store of setting, there were 4 type of setting, there are slider, page, store, and policy
 * 
 * @author cmooy
 */
class StoreSettingController extends Controller
{
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

		$result                     = $result->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
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

			return new JSend('success', (array)$result->toArray());
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
			return new JSend('error', (array)Input::all(), 'Tidak ada data setting.');
		}

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Validate StoreSetting Parameter
		$setting                    = Input::get('setting');
		
		if(is_null($setting['id']))
		{
			$is_new                 = true;
		}
		else
		{
			$is_new                 = false;
		}

		//2. Validate setting parameter
		//2a. Slider 
		if(!$errors->count() && $setting['type'] == 'slider')
		{
			$setting_data           = \App\Entities\Slider::findornew($setting['id']);

			$setting_rules          =   [
												'started_at'                => 'date_format:"Y-m-d H:i:s"',
												'ended_at'                  => 'date_format:"Y-m-d H:i:s"|after:started_at',
											];

			$validator              = Validator::make($setting, $setting_rules);
		}
		//2b. Page 
		elseif(!$errors->count() && in_array($setting['type'], ['about_us', 'why_join', 'term_and_condition']))
		{
			$setting_data           = \App\Entities\StorePage::findornew($setting['id']);

			$setting_rules          =   [
												'started_at'                => 'date_format:"Y-m-d H:i:s"',
											];

			$validator              = Validator::make($setting, $setting_rules);
		}
		//2c. Store 
		elseif(!$errors->count() && in_array($setting['type'], ['url', 'logo', 'facebook_url', 'twitter_url', 'instagram_url', 'email', 'phone', 'address', 'bank_information']))
		{
			$setting_data           = \App\Entities\Store::findornew($setting['id']);

			$setting_rules          =   [
												'started_at'                => 'date_format:"Y-m-d H:i:s"',
											];

			$validator              = Validator::make($setting, $setting_rules);
		}
		//2d. Policy 
		else
		{
			$setting_data           = \App\Entities\Policy::findornew($setting['id']);

			$setting_rules          =       [
												'started_at'                => 'date_format:"Y-m-d H:i:s"',
											];

			$validator              = Validator::make($setting, $setting_rules);
		}

		if (!$validator->passes())
		{
			$errors->add('StoreSetting', $validator->errors());
		}
		else
		{
			//if validator passed, save setting
			$setting_data           = $setting_data->fill($setting);

			if(!$setting_data->save())
			{
				$errors->add('StoreSetting', $setting_data->getError());
			}
		}

		//3. save image for slider
		if(!$errors->count() && isset($setting['images']) && is_array($setting['images']) && $setting_data['type']=='slider')
		{
			$image_current_ids         = [];
			foreach ($setting['images'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$image_data		= \App\Entities\Image::findornew($value['id']);

					$image_rules	=   [
											// 'imageable_id'              => 'exists:tmp_store_settings,id|'.($is_new ? '' : 'in:'.$setting_data['id']),
											// 'imageable_type'			=> ($is_new ? '' : 'in:'.get_class($setting_data)),
											'thumbnail'                 => 'required|max:255',
											'image_xs'                  => 'required|max:255',
											'image_sm'                  => 'required|max:255',
											'image_md'                  => 'required|max:255',
											'image_lg'                  => 'required|max:255',
											'is_default'                => 'boolean',
										];

					$validator      	= Validator::make($value, $image_rules);

					//if there was image and validator false
					if(!$validator->passes())
					{
						$errors->add('Image', $validator->errors());
					}
					else
					{
						$value['imageable_id']          = $setting_data['id'];
						$value['imageable_type']        = get_class($setting_data);

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

				//if there was no error, check if there were things need to be delete
				if(!$errors->count())
				{
					$images                            = \App\Entities\Image::imageableid($setting['id'])->get(['id'])->toArray();
					
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
							$image_data                = \App\Entities\Image::find($value);

							if(!$image_data->delete())
							{
								$errors->add('Image', $image_data->getError());
							}
						}
					}
				}
			}
		}

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		if($setting_data['type']=='slider')
		{
			$final_setting          = \App\Entities\Slider::id($setting_data['id'])->with(['images'])->first()->toArray();
		}
		else
		{
			$final_setting          = \App\Entities\StoreSetting::id($setting_data['id'])->first()->toArray();
		}

		return new JSend('success', (array)$final_setting);
	}
}
