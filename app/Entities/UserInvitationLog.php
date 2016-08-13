<?php

namespace App\Entities;

use App\CrossServices\ClosedDoorModelObserver;

use App\Entities\TraitRelations\BelongsToUserTrait;

class UserInvitationLog extends BaseModel
{
	/**
	 * Relationship Traits
	 *
	 */
	use BelongsToUserTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'user_invitation_logs';
	
	/**
	 * Date will be returned as carbon
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at'];

	/**
	 * The appends attributes from mutator and accessor
	 *
	 * @var array
	 */
	protected $appends				=	[];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 				= [];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'user_id'						,
											'email'							,
											'invitation_link'				,
											'is_used'						,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'user_id'						=> 'exists:users,id',
											'email'							=> 'required|max:255|email',
											'invitation_link'				=> 'max:255',
											'is_used'						=> 'boolean',
										];

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        UserInvitationLog::observe(new ClosedDoorModelObserver());
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
	 * find invitation link
	 * 
	 * @param invitation link
	 */	
	public function scopeInvitationLink($query, $variable)
	{
		return $query->where('invitation_link', $variable);
	}
}
