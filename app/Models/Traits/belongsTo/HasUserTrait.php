<?php 

namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs to user.
 *
 * @author cmooy
 */
trait HasUserTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasUserTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function User()
	{
		return $this->belongsTo('App\Models\User');
	}

	/**
	 * check if model has user
	 *
	 **/
	public function scopeHasUser($query, $variable)
	{
		return $query->whereHas('user', function($q)use($variable){$q;});
	}

	/**
	 * check if model has user in certain id
	 *
	 * @var string id
	 **/
	public function scopeUserID($query, $variable)
	{
		return $query->where('user_id', $variable);
	}

	/**
	 * check if model has user name
	 *
	 **/
	public function scopeUserName($query, $variable)
	{
		return $query->whereHas('user', function($q)use($variable){$q->name;});
	}

	/**
	 * call belongs to relationship with customer only
	 *
	 **/
	public function Customer()
	{
		return $this->belongsTo('App\Models\Customer', 'user_id');
	}

	/**
	 * check if model has customer in certain name
	 *
	 * @var string name
	 **/
	public function scopeCustomerName($query, $variable)
	{
		return $query
				->selectraw($this->getTable().'.*')
				->join('users', function ($join) use($variable) 
				{
	             	$join->on ( $this->getTable().'.user_id', '=', 'users.id' )
	             		->where('users.name', 'like', '%'.$variable.'%')
						->wherenull('users.deleted_at')
					;
				})
			;
	}

}