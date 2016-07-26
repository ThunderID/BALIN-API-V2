<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of cluster
 * 
 * @author cmooy
 */
class ClusterController extends Controller
{
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
     * Display a cluster
     *
     * @param type, cluster id
     * @return Response
     */
    public function detail($type = null, $id = null)
    {
        if($type=='category')
        {
            $result                 = \App\Models\Category::id($id)->orderby('path', 'asc')->with(['category', 'products'])->first();
        }
        else
        {
            $result                 = \App\Models\Tag::id($id)->orderby('path', 'asc')->with(['tag', 'products'])->first();
        }

        if($result)
        {
            return new JSend('success', (array)$result->toArray());
        }

        return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
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
            return new JSend('error', (array)Input::all(), 'Tidak ada data cluster.');
        }

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Validate Cluster Parameter
        if($type=='category')
        {
            $cluster                    = Input::get('category');
            $cluster_data               = \App\Models\Category::findornew($cluster['id']);
        }
        else
        {
            $cluster                    = Input::get('tag');
            $cluster_data               = \App\Models\Tag::findornew($cluster['id']);
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
            $cluster_data           = $cluster_data->fill(['name' => $cluster['name'], 'type' => $cluster_data->type, 'category_id' => (isset($cluster['category_id']) ? $cluster['category_id'] : 0)]);

            if(!$cluster_data->save())
            {
                $errors->add('Cluster', $cluster_data->getError());
            }
        }
        //End of validate cluster


        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

        DB::commit();
        
        $final_cluster              = \App\Models\Cluster::id($cluster_data['id'])->with(['category', 'products'])->first()->toArray();

        return new JSend('success', (array)$final_cluster);
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
            $cluster                = \App\Models\Category::id($id)->orderby('path', 'asc')->with(['category', 'products'])->first();
        }
        else
        {
            $cluster                = \App\Models\Tag::id($id)->orderby('path', 'asc')->with(['tag', 'products'])->first();
        }

        if(!$cluster)
        {
            return new JSend('error', (array)Input::all(), 'Category/Tag tidak ditemukan.');
        }

        $result                     = $cluster;

        if($cluster->delete())
        {
            return new JSend('success', (array)$result);
        }

        return new JSend('error', (array)$result, $cluster->getError());
    }
}
