<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Services\BalinManualStorePoint;

/**
 * Handle Protected display and store of point
 * 
 * @author cmooy
 */
class PointController extends Controller
{
	public function __construct(Request $request, BalinManualStorePoint $point)
	{
		$this->request 				= $request;
		$this->point 				= $point;
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

		//1. Validate Point Parameter
		$point				= Input::get('point');

		$point_store		= $this->point;
			
		$point_store->fill($point);

		if(!$point_store->save())
		{
			return response()->json( JSend::error($point_store->getError()->toArray())->asArray());
		}
		
		return response()->json( JSend::success($point_store->getData()->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}
}
