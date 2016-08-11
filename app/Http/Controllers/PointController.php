<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * Handle Protected display and store of point
 * 
 * @author cmooy
 */
class PointController extends Controller
{
	public function __construct(Request $request)
	{
		$this->request 				= $request;
	}
	
	/**
	 * Display all points
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index()
	{
		$result                 = new \App\Entities\PointLog;

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				if(Input::has('search'))
				{
					$search                 = Input::get('search');

					foreach ($search as $key => $value) 
					{
						switch (strtolower($key)) 
						{
							case 'customername':
								$result     = $result->customername($value);
								break;
							default:
								# code...
								break;
						}
					}
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
					case 'expired':
						$result     = $result->orderby('expired_at', $value);
						break;
					case 'amount':
						$result     = $result->orderby('amount', $value);
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

		$result                     = $result->with(['user'])->get();

		return response()->json( JSend::success(['count' => $count, 'data' => $result->toArray()])->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Store a point
	 *
	 * @return Response
	 */
	public function store()
	{
		if(!Input::has('point'))
		{
			return response()->json( JSend::error(['Tidak ada data point.'])->asArray());
		}

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Validate Point Parameter
		$point                       = Input::get('point');

		if(is_null($point['id']))
		{
			$is_new                 = true;
		}
		else
		{
			$is_new                 = false;
		}


		//1. Get original data
		$point_data                 = \App\Entities\PointLog::findornew($point['id']);

		if(!$errors->count())
		{
			$point_rules   =   [
												'user_id'                   => 'required|exists:users,id',
												'amount'                    => 'required|numeric',
												'expired_at'                => 'required|date_format:"Y-m-d H:i:s"',
												'notes'                     => 'required',
											];

			$validator      = Validator::make($point, $point_rules);

			if (!$validator->passes())
			{
				$errors->add('Point', $validator->errors());
			}
			else
			{
				$point_data                    = new \App\Entities\PointLog;

				$point_data                    = $point_data->fill($point);

				if(!$point_data->save())
				{
					$errors->add('Point', $point_data->getError());
				}
			}
		}

		if($errors->count())
		{
			DB::rollback();

			return response()->json( JSend::error($errors)->asArray());
		}

		DB::commit();
		
		$final_point                 = \App\Entities\PointLog::id($point_data['id'])->with(['user'])->first();
		
		return response()->json( JSend::success($final_point->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}
}
