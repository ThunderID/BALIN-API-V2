<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Models\Image;

/**
 * Used in Image model
 *
 * @author cmooy
 */
class ImageObserver 
{
    /** 
     * observe image event saving
     * 1. check default image to set init default
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
    {
        //1. check default image to set init default
		if(isset($model->imageable_id))
        {
            $countimage                 = Image::where('imageable_id', $model->imageable_id)
                                            ->where('imageable_type', $model->imageable_type)
                                            ->where('is_default', 1)
                                            ->count();
            if($countimage == 0)
            {
                $model->is_default     = 1;
            }
        }

        return true;
    }

    /** 
     * observe image event saved
     * 1. check default image and make sure it's the only default
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function saved($model)
    {
        //1. check default image event
		if(isset($model->imageable_id) && $model->is_default == 1)
        {
            $images                     = Image::where('imageable_id', $model->imageable_id)
                                            ->where('imageable_type', $model->imageable_type)
                                            ->where('is_default', 1)
                                            ->where('id','!=', $model->id)
                                            ->get();

            foreach ($images as $image) 
            {
                //1a. set is_default to false for other image
               $image->fill([
                    'is_default'        => 0,
                ]);

               if(!$image->save())
               {
                    $model['errors']    = $image->geterror();

                    return false;
               }
            }
        }

        return true;
    }
}
