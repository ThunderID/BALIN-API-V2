<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\StoreProductInterface;

use App\Contracts\Policies\ValidatingProductInterface;
use App\Contracts\Policies\ProceedProductInterface;
use App\Contracts\Policies\EffectProductInterface;

use App\Entities\Product;

class BalinStoreProduct implements StoreProductInterface 
{
	protected $product;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $post;
	protected $pro;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(ValidatingProductInterface $pre, ProceedProductInterface $pro, EffectProductInterface $post)
	{
		$this->errors 	= new MessageBag;
		$this->pre 		= $pre;
		$this->pro 		= $pro;
		$this->post 	= $post;
	}

	/**
	 * return errors
	 *
	 * @return MessageBag
	 **/
	function getError()
	{
		return $this->errors;
	}

	/**
	 * return saved_data
	 *
	 * @return saved_data
	 **/
	function getData()
	{
		return $this->saved_data;
	}

	/**
	 * Checkout
	 *
	 * 1. Call Class fill
	 * 
	 * @return Response
	 */
	public function fill(array $product)
	{
		$this->product 		= $product;
	}

	/**
	 * Save
	 *
	 * Here's the workflow
	 * 
	 * @return Response
	 */
	public function save()
	{
		/** PREPROCESS */

		//1. Validate product
		$this->pre->validateproduct($this->product); 

		//2. Validate varians
		if(isset($this->product['varians']))
		{
			$this->pre->validatevarian($this->product['varians']); 
		}

		//3. Validate prices
		$this->pre->validateprice($this->product['prices']); 

		//4. Validate labels
		if(isset($this->product['labels']))
		{
			$this->pre->validatelabel($this->product['labels']); 
		}

		//5. Validate clusters
		$this->pre->validatecluster(array_merge($this->product['tags'], $this->product['categories'])); 

		//6. Validate images
		$this->pre->validateimage($this->product['images']); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//7. store product
		$this->product['slug']			= $this->pre->Getslug();
		$this->pro->storeproduct($this->product); 

		//8. store varians
		if(isset($this->product['varians']))
		{
			$this->pro->storevarian($this->pro->product, $this->product['varians']); 
		}
		
		//9. store prices
		$this->pro->storeprice($this->pro->product, $this->product['prices']); 

		//10. store labels
		if(isset($this->product['labels']))
		{
			$this->pro->storelabel($this->pro->product, $this->product['labels']); 
		}
		
		//11. store clusters
		$this->pro->storecluster($this->pro->product, array_merge($this->product['tags'], $this->product['categories'])); 

		//12. store images
		$this->pro->storeimage($this->pro->product, $this->product['images']); 

		if($this->pro->errors->count())
		{
			\DB::rollback();
			
			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//13. Return product Model Object
		$this->saved_data	= $this->pro->product;

		return true;
	}
}
