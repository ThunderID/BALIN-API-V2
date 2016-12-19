<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models has many QuotaLogLogs.
 *
 * @author cmooy
 */
trait HasManyQuotaLogsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasManyQuotaLogsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function QuotaLogs()
	{
		return $this->hasMany('App\Entities\QuotaLog');
	}

	/**
	 * check if model has quota log
	 *
	 **/
	public function scopeHasQuotaLogs($query, $variable)
	{
		return $query->whereHas('quotalogs', function($q)use($variable){$q;});
	}

	/**
	 * check if model has quota log in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeQuotaLogID($query, $variable)
	{
		return $query->whereHas('quotalogs', function($q)use($variable){$q->id($variable);});
	}
}