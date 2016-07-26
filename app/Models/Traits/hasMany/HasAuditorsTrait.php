<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Auditors.
 *
 * @author cmooy
 */
trait HasAuditorsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasAuditorsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Audits()
	{
		return $this->hasMany('App\Models\Auditor', 'user_id');
	}

	/**
	 * check if model has auditor
	 *
	 **/
	public function scopeHasAuditors($query, $variable)
	{
		return $query->whereHas('pointlogs', function($q)use($variable){$q;});
	}

	/**
	 * check if model has auditor in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeAuditorID($query, $variable)
	{
		return $query->whereHas('pointlogs', function($q)use($variable){$q->id($variable);});
	}
}