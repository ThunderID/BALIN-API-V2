<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Tool to help broadcasting process
 * 
 * @author cmooy
 */
class BroadcastController extends Controller
{
	/**
	 * Display all queues
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function queue()
	{
		$user						= \LucaDegasperi\OAuth2Server\Facades\Authorizer::getResourceOwnerId();
		$user						= json_decode($user, true)['data'];

		if($user)
		{
			$userid					= $user['id'];
		}
		else
		{
			\App::abort(404);
		}

		$result						= new \App\Models\Queue;
		
		$result 					= $result->userid($userid);

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'running':
						$result     = $result->running($value);
						break;
					case 'complete':
						$result     = $result->complete($value);
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
					case 'newest':
						$result     = $result->orderby('created_at', $value);
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

		$result                     = $result->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Store a queue
	 *
	 * 1. Validate Price Parameter
	 * 
	 * @return Response
	 */
	public function price()
	{
		if(!Input::has('price'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data price.');
		}
		
		$user                       = \LucaDegasperi\OAuth2Server\Facades\Authorizer::getResourceOwnerId();
		$user                       = json_decode($user, true)['data'];

		if($user)
		{
			$userid             = $user['id'];
		}
		else
		{
			\App::abort(404);
		}


		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. Validate Price Parameter
		$price						= Input::get('price');

		$price_rules				=   [
											'discount_amount'		=> 'required_without:discount_percentage|numeric',
											'discount_percentage'	=> 'required_without:discount_amount|numeric',
											'started_at'			=> 'required|date_format:"Y-m-d H:i:s"',
											'ended_at'				=> 'required|date_format:"Y-m-d H:i:s"|after:started_at',
											'category_ids'			=> 'required_if:item,category|array',
											'tag_ids'				=> 'required_if:item,tag|array',
											'is_labeled'			=> 'boolean',
										];

		$validator                  = Validator::make($price, $price_rules);

		if (!$validator->passes())
		{
			$errors->add('Price', $validator->errors());
		}
		else
		{
			$products 				= new \App\Models\Product;
			$products 				= $products->sellable(true);

			if(isset($price['category_ids']))
			{
				$products 			= $products->categoriesid($price['category_ids']);
			}
			elseif(isset($price['tag_ids']))
			{
				$products 			= $products->tagsid($price['tag_ids']);
			}

			$products 				= $products->get(['id']);

			$parameter				= $price;

			$queue 					= new \App\Models\Queue;
			$queue->fill([
				'user_id'			=> $userid,
				'process_name'		=> 'broadcast:discount',
				'parameter'			=> json_encode($parameter),
				'total_process'		=> count($products),
				'task_per_process'	=> 1,
				'process_number'	=> 0,
				'total_task'		=> count($products),
				'message'			=> 'Initial Commit',
			]);

			if(!$queue->save())
			{
				$errors->add('Product', $queue->getError());
			}
		}
		//End of validate price

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_queue				= \App\Models\Queue::id($queue['id'])->first()->toArray();

		return new JSend('success', (array)$final_queue);
	}
}
