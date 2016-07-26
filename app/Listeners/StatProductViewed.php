<?php

namespace App\Listeners;

use App\Events\ProductSearched;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

/**
 * Listener for Product Viewed
 * 
 * @author cmooy
 */
class StatProductViewed
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * 1. check stat type
	 * 2. check every item searched (slug)
	 * 3. check stat
	 * 4. if there were no error save
	 *
	 * @param  ProductSearched  $event
	 * @return void
	 */
	public function handle(ProductSearched $event)
	{
		// Access the stat using $event->stat...
		//1. check stat type
		if($event->stat['type']=='category')
		{
			$data                   = new \App\Models\Category;
		}
		elseif($event->stat['type']=='tag')
		{
			$data                   = new \App\Models\Tag;
		}
		elseif($event->stat['type']=='product')
		{
			$data                   = new \App\Models\Product;
		}

		//2. check every item searched
		$checkslug                          = [];
		if(is_array($event->stat['slug']))
		{
			foreach ($event->stat['slug'] as $key => $value) 
			{
				$check                      = $this->checkslug($data, $value);
				if($check)
				{
					$checkslug[$value]      = $check;
				}
			}
		}
		else
		{
			$check                                  = $this->checkslug($data, $event->stat['slug']);
			if($check)
			{
				$checkslug[$event->stat['slug']]    = $check;
			}
		}

		//3. check stat user view
		if(isset($event->stat['user_id']))
		{
			$userid                     = $event->stat['user_id'];
		}
		else
		{
			$userid                     = 0;
		}

		foreach ($checkslug as $key => $value) 
		{
			$statuser					= new \App\Models\StatUserView;

			$statuser->fill(['user_id' => $userid, 'statable_id' => $value['id'], 'statable_type' => get_class($data)]);
			
			$statuser->save();
		}

		return true;
	}

	/**
	 * Check stat slug.
	 *
	 * @param $model (of data), $slug
	 * @return false or variable
	 */
	public function CheckSlug($data, $slug)
	{
		return $data->slug($slug)->first();
	}
}