<?php

namespace App\Entities;

use App\Entities\Traits\HasPointDiscountTrait;
use App\Entities\Traits\HasExtendCostTrait;
use App\Entities\Traits\HasAmountTrait;
use App\Entities\Traits\HasBillTrait;
use App\Entities\Traits\HasStatusTrait;
use App\Entities\Traits\IsSellTrait;

use App\Entities\Traits\GetAllTrait;

use App\Entities\TraitLibraries\JoinTransactionTrait;
use App\Entities\TraitLibraries\JoinProductTrait;
use App\Entities\TraitLibraries\JoinShipmentTrait;
use App\Entities\TraitLibraries\SelectProductNotesTrait;
use App\Entities\TraitLibraries\SelectAddressNotesTrait;
use App\Entities\TraitLibraries\FieldTransactionTrait;

use App\Services\Entities\TraitLibraries\SaleTrait;

use App\Entities\TraitRelations\BelongsToUserTrait;
use App\Entities\TraitRelations\BelongsToVoucherTrait;
use App\Entities\TraitRelations\HasOnePaymentTrait;
use App\Entities\TraitRelations\HasOneShipmentTrait;
use App\Entities\TraitRelations\HasManyPointLogsTrait;
use App\Entities\TraitRelations\HasManyTransactionDetailsTrait;
use App\Entities\TraitRelations\HasManyTransactionExtensionsTrait;
use App\Entities\TraitRelations\HasManyTransactionLogsTrait;

use App\CrossServices\ClosedDoorModelObserver;

/** 
	* Inheritance Transaction Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Sale extends Transaction
{
	/**
	 * Traits To Calculated Relations
	 */
	use HasPointDiscountTrait;
	use HasExtendCostTrait;
	use HasAmountTrait;
	use HasBillTrait;

	use HasStatusTrait;
	
	/**
	 * Traits To Condition within itself
	 */
	use IsSellTrait;

	use GetAllTrait;

	/**
	 * Libraries Traits for scopes
	 *
	 */
	use JoinTransactionTrait;
	use JoinProductTrait;
	use JoinShipmentTrait;
	use SelectProductNotesTrait;
	use SelectAddressNotesTrait;
	use FieldTransactionTrait;

	use SaleTrait;

	/**
	 * Relationship Traits
	 *
	 */
	use BelongsToUserTrait;
	use BelongsToVoucherTrait;
	use HasOnePaymentTrait;
	use HasOneShipmentTrait;
	use HasManyPointLogsTrait;
	use HasManyTransactionDetailsTrait;
	use HasManyTransactionExtensionsTrait;
	use HasManyTransactionLogsTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'user_id'						,
											'voucher_id'					,
											'ref_number'					,
											'type'							,
											'transact_at'					,
											'unique_number'					,
											'shipping_cost'					,
											'voucher_discount'				,
										];
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'user_id'						=> 'exists:users,id',
											'type'							=> 'in:sell',
											'ref_number'					=> 'max:255',
											'transact_at'					=> 'date_format:"Y-m-d H:i:s"',
											'unique_number'					=> 'numeric',
											'shipping_cost'					=> 'numeric',
											'voucher_discount'				=> 'numeric',
										];
	
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 				= ['supplier_id'];

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

        Sale::observe(new ClosedDoorModelObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
