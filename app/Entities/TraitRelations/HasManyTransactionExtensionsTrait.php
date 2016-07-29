<?php

namespace App\Entities\TraitRelations;

use Illuminate\Support\Pluralizer;

/**
 * Trait for models has one TransactionExtension.
 *
 * @author cmooy
 */
trait HasManyTransactionExtensionsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasManyTransactionExtensionsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function TransactionExtensions()
	{
		return $this->hasMany('App\Entities\TransactionExtension', Pluralizer::singular($this->getTable()).'_id');
	}
}
