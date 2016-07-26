<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Models\Product;
use App\Models\Varian;
use App\Models\CategoryProduct;
use App\Models\ProductLabel;
use App\Models\Image;
use App\Models\Price;

/**
 * Used in Product model
 *
 * @author cmooy
 */
class ProductObserver 
{
    /** 
     * observe product event saving
     * 1. check unique slug
     * 2. check unique upc
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
    {
		$errors 						= new MessageBag();

		//1. Check unique Slug
        $model->slug            		= Str::slug($model->name);

        $slug                           = Product::slug($model->slug)->notid($model->id)->first();

        if(!is_null($slug))
        {
			$errors->add('slug', 'Produk sudah terdaftar.');
        }
        
		//2. Check unique UPC
        $upc                            = Product::upc($model->upc)->notid($model->id)->first();

        if(!is_null($upc))
        {
			$errors->add('slug', 'UPC sudah terdaftar.');
        }
        
        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
    }

    /** 
     * observe product event deleting
     * 1. delete varian
     * 2. delete price
     * 3. delete category(and tag)
     * 4. delete label
     * 5. delete image
     * 6. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function deleting($model)
    {
		$errors 						= new MessageBag();

        //1. delete product's varian
        $varians                        = Varian::where('product_id', $model->id)->get();

        foreach ($varians as $varian) 
        {
            if(!$varian->delete())
            {
                $errors->add('varian', $varian->getError());
            }                     
        }

        //2. delete product's price
        $prices                        = Price::where('product_id', $model->id)->get();

        foreach ($prices as $price) 
        {
            if(!$price->delete())
            {
                $errors->add('price', $price->getError());
            }                     
        }

        //3. delete product's categories
        $categories                     = CategoryProduct::where('product_id', $model->id)->get();

        foreach ($categories as $category) 
        {
            if(!$category->delete())
            {
                $errors->add('category', $category->getError());
            }                     
        }

        //4. delete product's label
        $labels                        = ProductLabel::where('product_id', $model->id)->get();

        foreach ($labels as $label) 
        {
            if(!$label->delete())
            {
                $errors->add('label', $label->getError());
            }                     
        }

        //5. delete product's image
        $images                        = Image::where('imageable_type','App\Models\Product')->where('imageable_id', $model->id)->get();

        foreach ($images as $image) 
        {
            if(!$image->delete())
            {
                $errors->add('image', $image->getError());
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
