<?php

namespace App\Entities;

use App\Entities\Traits\HasAmountTrait;
use App\Entities\Traits\HasStatusTrait;
use App\Entities\Traits\IsBuyTrait;

use App\Entities\Traits\GetAllTrait;

use App\Entities\TraitLibraries\JoinTransactionTrait;
use App\Entities\TraitLibraries\FieldTransactionTrait;

use App\Entities\TraitRelations\BelongsToSupplierTrait;
use App\Entities\TraitRelations\HasManyTransactionDetailsTrait;
use App\Entities\TraitRelations\HasManyTransactionLogsTrait;

use App\CrossServices\ClosedDoorModelObserver;

/** 
	* Inheritance Transaction Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Purchase extends Transaction
{
	/**
	 * Traits To Calculated Relations
	 */
	use HasAmountTrait;

	use HasStatusTrait;
	
	/**
	 * Traits To Condition within itself
	 */
	use IsBuyTrait;

	use GetAllTrait;

	/**
	 * Libraries Traits for scopes
	 *
	 */
	use JoinTransactionTrait;
	use FieldTransactionTrait;
	
	/**
	 * Relationship Traits
	 *
	 */
	use BelongsToSupplierTrait;
	use HasManyTransactionDetailsTrait;
	use HasManyTransactionLogsTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'supplier_id'					,
											'ref_number'					,
											'type'							,
											'transact_at'					,
										];
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'supplier_id'					=> 'exists:suppliers,id',
											'type'							=> 'required|in:buy',
											'ref_number'					=> 'max:255',
											'transact_at'					=> 'date_format:"Y-m-d H:i:s"',
										];
	
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 				= ['user_id', 'voucher_id', 'shipping_cost', 'voucher_discount', 'unique_number'];

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/**
	 * boot
	 * observing model
	 *
	 */
	public static function boot() 
	{
        parent::boot();

        Purchase::observe(new ClosedDoorModelObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
