<?php namespace App\Models\Traits\hasMany;

use DB;
use Illuminate\Support\Pluralizer;

/**
 * Trait for models has one TransactionExtension.
 *
 * @author cmooy
 */
trait HasTransactionExtensionsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasTransactionExtensionsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function TransactionExtensions()
	{
		return $this->hasMany('App\Models\TransactionExtension', Pluralizer::singular($this->getTable()).'_id');
	}
}
