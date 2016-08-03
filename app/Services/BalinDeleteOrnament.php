<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\DeleteOrnamentInterface;

use App\Contracts\Policies\ValidatingOrnamentInterface;
use App\Contracts\Policies\ProceedOrnamentInterface;
use App\Contracts\Policies\EffectOrnamentInterface;

use App\Entities\ProductExtension as Ornament;

class BalinDeleteOrnament implements DeleteOrnamentInterface 
{
	protected $ornament;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $post;
	protected $pro;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(ValidatingOrnamentInterface $pre, ProceedOrnamentInterface $pro, EffectOrnamentInterface $post)
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
	public function delete(Ornament $ornament)
	{
		$this->Ornament 			= $ornament->toArray();
		
		/** PREPROCESS */

		//1. Validate Ornament
		$this->pre->validatedeleteornament($ornament); 

		if($this->pre->errors->count())
		{
			$this->errors 		= $this->pre->errors;

			return false;
		}

		\DB::BeginTransaction();

		/** PROCESS */

		//2. Delete Ornaments
		$this->pro->deleteornament($ornament); 

		if($this->pro->errors->count())
		{
		
			\DB::rollback();
		
			$this->errors 		= $this->pro->errors;

			return false;
		}

		\DB::commit();

		//3. Return Ornament Model Object
		$this->saved_data	= $this->ornament;

		return true;
	}
}
