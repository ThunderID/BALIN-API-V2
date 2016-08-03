<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\DeleteProductInterface;

use App\Contracts\Policies\ValidatingProductInterface;
use App\Contracts\Policies\ProceedProductInterface;
use App\Contracts\Policies\EffectProductInterface;

use App\Entities\Product;

class BalinDeleteProduct implements DeleteProductInterface 
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
	 * Save
	 *
	 * Here's the workflow
	 * 
	 * @return Response
	 */
	public function delete(Product $product)
	{
		$this->product 			= $product->toArray();
		
		/** PREPROCESS */

		//1. Validate product
		$this->pre->validatedeleteproduct($product); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//2. Delete varians
		$this->pro->deletevarian($product); 

		//3. Delete prices
		$this->pro->deleteprice($product); 

		//4. Delete labels
		$this->pro->deletelabel($product); 

		//5. Delete clusters
		$this->pro->deletecluster($product); 

		//6. Delete images
		$this->pro->deleteimage($product); 

		//7. Delete product
		$this->pro->deleteproduct($product); 

		if($this->pro->errors->count())
		{
			\DB::rollback();
			
			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//8. Return product Model Object
		$this->saved_data	= $this->product;

		return true;
	}
}
