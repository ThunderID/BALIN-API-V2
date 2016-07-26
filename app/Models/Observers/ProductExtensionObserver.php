<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Models\ProductExtension;
use App\Models\Product;
use App\Models\Image;

/**
 * Used in ProductExtension model
 *
 * @author cmooy
 */
class ProductExtensionObserver 
{
	/** 
	 * observe product extension event saving
	 * 1. check unique upc
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function saving($model)
	{
		$errors 						= new MessageBag();

		//1. Check unique UPC
		// $upc                            = Product::upc($model->upc)->notid($model->id)->first();

		// if(!is_null($upc))
		// {
		// 	$errors->add('slug', 'UPC sudah terdaftar.');
		// }
		// else
		// {
		// 	$upc                        = ProductExtensionExtension::upc($model->upc)->notid($model->id)->first();
		// 	if(!is_null($upc))
		// 	{
		// 		$errors->add('slug', 'UPC sudah terdaftar.');
		// 	}
		// }

		if($errors->count())
		{
			$model['errors'] 		= $errors;

			return false;
		}

		return true;
	}

	/** 
	 * observe product extension event deleting
	 * 1. check transaction
	 * 2. delete image
	 * 3. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function deleting($model)
	{
		$errors 						= new MessageBag();

		//1. check transaction
		$transaction					= TransactionExtension::productextensionid($model->id)->first();

		if($transaction)
		{
			$errors->add('Product', 'Tidak dapat menghapus produk yang sudah pernah dibeli.');
		}

		//2. delete product extension's image
		$images							= Image::where('imageable_type', 'App\Models\ProductExtension')->where('imageable_id', $model->id)->get();

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
