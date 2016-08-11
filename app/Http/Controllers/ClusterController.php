<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * Handle Protected Resource of cluster
 * 
 * @author cmooy
 */
class ClusterController extends Controller
{
	public function __construct(Request $request)
	{
		$this->request 				= $request;
	}
	
	/**
	 * Display all clusters
	 *
	 * @param type, search, skip, take
	 * @return Response
	 */
	public function index($type = null)
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
	 * Display a cluster
	 *
	 * @param type, cluster id
	 * @return Response
	 */
	public function detail($type = null, $id = null)
	{
		if($type=='category')
		{
			$result                 = \App\Entities\Category::id($id)->orderby('path', 'asc')->with(['category', 'products'])->first();
		}
		else
		{
			$result                 = \App\Entities\Tag::id($id)->orderby('path', 'asc')->with(['tag', 'products'])->first();
		}

		if($result)
		{
			return response()->json( JSend::success($result->toArray())->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::error(['ID Tidak Valid.'])->asArray());
	}

	/**
	 * Store a cluster
	 *
	 * @param type
	 * @return Response
	 */
	public function store($type = null)
	{
		if(!Input::has('category') && !Input::has('tag'))
		{
			return response()->json( JSend::error(['Tidak ada data cluster.'])->asArray());
		}

		$errors 						= new MessageBag;
		
		//1. Validate Cluster Parameter
		if($type=='category')
		{
			$cluster                    = Input::get('category');
			$cluster_data               = \App\Entities\Category::findornew($cluster['id']);
		}
		else
		{
			$cluster                    = Input::get('tag');
			$cluster_data               = \App\Entities\Tag::findornew($cluster['id']);
		}

		//1a. Get original data
		if(is_null($cluster['id']))
		{
			$is_new                 = true;
		}
		else
		{
			$is_new                 = false;
		}


		$cluster_rules             =   [
											'category_id'               => 'numeric|exists:categories,id',
											'name'                      => 'required|max:255',
											'slug'                      => 'max:255|unique:categories,slug,'.(!is_null($cluster['id']) ? $cluster['id'] : ''),
										];

		//1b. Validate Basic Cluster Parameter
		$validator                  = Validator::make($cluster, $cluster_rules);

		if (!$validator->passes())
		{
			$errors->add('Cluster', $validator->errors());
		}
		else
		{
			//if validator passed, save cluster
			$cluster_data           = $cluster_data->fill(['name' => $cluster['name'], 'type' => $type, 'category_id' => (isset($cluster['category_id']) ? $cluster['category_id'] : 0)]);

			if(!$cluster_data->save())
			{
				$errors->add('Cluster', $cluster_data->getError());
			}
		}
		//End of validate cluster


		if($errors->count())
		{
			DB::rollback();

			return response()->json( JSend::error($errors)->asArray());
		}

		DB::commit();
		
		if(str_is('*Category', get_class($cluster_data)))
		{
			$final_cluster              = \App\Entities\Category::id($cluster_data['id'])->with(['category', 'products'])->first();
		}
		else
		{
			$final_cluster              = \App\Entities\Tag::id($cluster_data['id'])->with(['category', 'products'])->first();
		}

		return response()->json( JSend::success($final_cluster->toArray())->asArray())
					->setCallback($this->request->input('callback'));
	}

	/**
	 * Delete a cluster
	 *
	 * @param type, cluster id
	 * @return Response
	 */
	public function delete($type = null, $id = null)
	{
		//
		if($type=='category')
		{
			$cluster                = \App\Entities\Category::id($id)->orderby('path', 'asc')->with(['category', 'products'])->first();
		}
		else
		{
			$cluster                = \App\Entities\Tag::id($id)->orderby('path', 'asc')->with(['tag', 'products'])->first();
		}

		if(!$cluster)
		{
			return response()->json( JSend::error(['Category/Tag tidak ditemukan.'])->asArray());
		}

		$result                     = $cluster->toArray();

		if($cluster->delete())
		{
			return response()->json( JSend::success($result)->asArray())
					->setCallback($this->request->input('callback'));
		}

		return response()->json( JSend::error($cluster->getError()));
	}
}
