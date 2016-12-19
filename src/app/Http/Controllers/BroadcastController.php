<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use \GenTux\Jwt\GetsJwtToken;

use App\Entities\Admin;

/**
 * Tool to help broadcasting process
 * 
 * @author cmooy
 */
class BroadcastController extends Controller
{
	use GetsJwtToken;
	
	public function __construct(Request $request)
	{
		$this->request 				= $request;
	}

	/**
	 * Display all queues
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function queue()
	{
        $payload                    = $this->jwtPayload();

		$user						= Admin::find($payload['context']['id']);

		if($user)
		{
			$userid					= $user['id'];
		}
		else
		{
			\App::abort(404);
		}

		$result						= new \App\Entities\Queue;
		
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
					return response()->json( JSend::error([$key.' harus bernilai asc atau desc.'])->asArray());
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

		$count						= count($result->get());

		if(Input::has('skip'))
		{
			$skip					= Input::get('skip');
			$result					= $result->skip($skip);
		}

		if(Input::has('take'))
		{
			$take					= Input::get('take');
			$result					= $result->take($take);
		}

		$result						= $result->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
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
			return response()->json( JSend::error(['Tidak ada data price.'])->asArray());
		}
		
		$payload				= $this->jwtPayload();

		$user					= Admin::find($payload['context']['id']);

		if($user)
		{
			$userid				= $user['id'];
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
			$products 				= new \App\Entities\Product;
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

			$queue 					= new \App\Entities\Queue;
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

			return response()->json( JSend::error($errors->toArray())->asArray());
		}

		DB::commit();
		
		$final_queue				= \App\Entities\Queue::id($queue['id'])->first();

		return response()->json( JSend::success($final_queue->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}
}
