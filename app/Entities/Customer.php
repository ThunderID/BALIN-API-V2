<?php

namespace App\Entities;

use App\CrossServices\ClosedDoorModelObserver;

use App\Entities\Traits\HasTotalPointTrait;
use App\Entities\Traits\HasCodeReferralTrait;
use App\Entities\Traits\HasReferenceNameTrait;
use App\Entities\Traits\HasTotalReferenceTrait;

use App\Entities\Traits\IsCustomerTrait;
use App\Entities\Traits\GetAllTrait;

use App\Entities\TraitLibraries\FieldNameTrait;

use App\Entities\TraitRelations\HasManyTransactionsTrait;
use App\Entities\TraitRelations\HasManyPointLogsTrait;
use App\Entities\TraitRelations\HasOneReferralTrait;

/** 
	* Inheritance User Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Customer extends User
{
	/**
	 * Traits To Calculated Relations
	 */
	use HasTotalPointTrait;
	use HasCodeReferralTrait;
	use HasReferenceNameTrait;
	use HasTotalReferenceTrait;

	/**
	 * Traits To Condition within itself
	 */
	use IsCustomerTrait;

	use GetAllTrait;

	/**
	 * Libraries Traits for scopes
	 *
	 */
	use FieldNameTrait;

	/**
	 * Relationship Traits
	 *
	 */
	use HasManyTransactionsTrait;
	use HasManyPointLogsTrait;
	use HasOneReferralTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'name'							,
											'email'							,
											'password'						,
											'role'							,
											'is_active'						,
											'sso_id'						,
											'sso_media'						,
											'sso_data'						,
											'gender'						,
											'date_of_birth'					,
											'activation_link'				,
											'reset_password_link'			,
											'expired_at'					,
											'last_logged_at'				,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'name'							=> 'required|max:255',
											'email'							=> 'max:255|email',
											// 'date_of_birth'					=> 'date_format:"Y-m-d H:i:s"|before:13 years ago'
										];
	
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 				= ['password', 'remember_token', 'expired_at'];

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        Customer::observe(new ClosedDoorModelObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope to get condition where activation link
	 *
	 * @param string or array of entity' activation link
	 **/
	public function scopeActivationLink($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn($query->getModel()->table.'.activation_link', $variable);
		}
		return 	$query->where($query->getModel()->table.'.activation_link', $variable);
	}

	/**
	 * scope to get condition where activation link
	 *
	 * @param string or array of entity' activation link
	 **/
	public function scopeSSOMedia($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn($query->getModel()->table.'.sso_media', $variable);
		}
		return 	$query->where($query->getModel()->table.'.sso_media', $variable);
	}

	/**
	 * scope to get condition where reset password link
	 *
	 * @param string or array of entity' reset password link
	 **/
	public function scopeResetPasswordLink($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn($query->getModel()->table.'.reset_password_link', $variable);
		}
		return 	$query->where($query->getModel()->table.'.reset_password_link', $variable);
	}

}
