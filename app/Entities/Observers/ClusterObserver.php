<?php 

namespace App\Entities\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Entities\CategoryCluster as Cluster;

/**
 * Used in Cluster, Tag, Category model
 *
 * @author cmooy
 */
class ClusterObserver 
{
    /** 
     * observe cluster event created
     * 1. modify path
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function created($model)
    {
        //1.modify path
        if($model->cluster()->count())
        {
            $model->path           = $model->cluster->path.','.$model->id;
        }
        else
        {
            $model->path           = $model->id;
        }

        if(!$model->save())
        {
            $model['errors']        = $model->getError();

            return false;
        }

        return true;
    }

    /** 
     * observe cluster event saving
     * 1. modify path
     * 2. check slug
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function saving($model)
    {
        //1.modify slug
        if(isset($model->category_id) && $model->category_id != 0 )
        {
            $model->slug                = Str::slug($model->CategoryCluster->name.' '.$model->name);
        }
        else
        {
            $model->slug                = Str::slug($model->name);
        }

        if(is_null($model->id))
        {
            $id                         = 0;
        }
        else
        {
            $id                         = $model->id;
        }

        //2. check slug
        $category                       = get_class($model)::slug($model->slug)->notid($id)->first();

        if($category)
        {
            $model['errors']            = 'Kategori/tag sudah terdaftar.';
        
            return false;
        }

        return true;
    }

    /** 
     * observe cluster event updating
     * 1. updated parent + child path
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function updating($model)
    {
        //1. check parent
        if(isset($model->getDirty()['category_id']) || !isset($model->getDirty()['path']))
        {
            //1a. mengganti path
            if($model->cluster()->count())
            {
                $model->path = $model->cluster->path . "," . $model->id;
            }
            else
            {
                $model->path = $model->id;
            }

            if(isset($model->getOriginal()['path']))
            {
                //1b. mengganti semua path child
                $childs                         = get_class($model)::orderBy('path','asc')
                                                    ->where('path','like',$model->getOriginal()['path'] . ',%')
                                                    ->where('type', $model->type)
                                                    ->get();

                foreach ($childs as $child) 
                {
                    $child->update(['path' => preg_replace('/'. $model->getOriginal()['path'].',/', $model->path . ',', $child->path,1)]);  
                }
            }
        }

        return true;
    }

    /** 
     * observe cluster event deleting
     * 1. updated parent + child path
     * 2. Delete Child
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function deleting($model)
    {
		$errors                           = new MessageBag();

        //1. Check varian relationship with transaction
        if($model->products->count())
        {
            $errors->add('cluster', 'Tidak dapat menghapus data yang berhubungan dengan produk varian yang pernah di stok &/ order.');
        }

        //2. Delete Child
        $childs                         = Cluster::orderBy('path','desc')
                                            ->where('path','like',$model->path . ',%')
                                            ->get();

        foreach ($childs as $child) 
        {
            if(!$child->delete())
            {
                $errors->add('cluster', $child->getError());
            }
        }

        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
    }
}
