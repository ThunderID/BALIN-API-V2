<?php

namespace App\Entities;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use App\CrossServices\ClosedDoorModelObserver;

/**
 * Used for User Models
 * 
 * @author cmooy
 */
class User extends BaseModel implements AuthenticatableContract, CanResetPasswordContract 
{
	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'users';

	/**
	 * Date will be returned as carbon
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'joined_at', 'expired_at', 'date_of_birth', 'last_logged_at'];

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	/**
	 * boot
	 * observing model
	 *
	 */	
	public static function boot() 
	{
		parent::boot();
 
        User::observe(new ClosedDoorModelObserver());
	}

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * find email
	 * 
	 * @param email
	 */	
	public function scopeEmail($query, $variable)
	{
		return $query->where('email', $variable);
	}

	/**
	 * find Active
	 * 
	 * @param Active
	 */	
	public function scopeActive($query, $variable)
	{
		return $query->where('is_active', $variable);
	}

	/**
	 * scope search based on role
	 *
	 * @param string or array of role
	 */	
	public function scopeRole($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn($query->getModel()->table.'.role', $variable);
		}

		if(is_null($variable))
		{
			return $query;
		}

		return 	$query->where($query->getModel()->table.'.role', $variable);
	}

	/**
	 * find reset password link
	 * 
	 * @param reset password link
	 */	
	public function scopeResetPasswordLink($query, $variable)
	{
		return $query->where('reset_password_link', $variable);
	}

	/**
	 * find sso media
	 * 
	 * @param sso media
	 */	
	public function scopeSSOMedia($query, $variable)
	{
		return $query->whereIn('sso_media', $variable);
	}

	/**
	 * find not sso media
	 * 
	 * @param sso media
	 */	
	public function scopeNotSSOMedia($query, $variable)
	{
		return $query->whereNotIn('sso_media', $variable);
	}
}
