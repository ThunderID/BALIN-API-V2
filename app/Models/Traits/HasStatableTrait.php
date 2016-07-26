<?php namespace App\Models\Traits;

/**
 * Trait for models has many scopeStats.
 *
 * @author cmooy
 */
trait HasStatableTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasStatableTraitConstructor()
	{
		//
	}
	
	/**
	 * call has many relationship
	 *
	 **/
	public function scopeStats($query, $variable = 0)
	{
		return $query->selectraw('IFNULL(SUM(stat_user_views.statable_id), 0) as views')
			->leftjoin('stat_user_views', function ($join) use($variable) 
			{
				$join->on ( $this->getTable().'.id', '=', 'stat_user_views.statable_id' )
					->where('statable_type', '=', get_class($this))
					->where('stat_user_views.user_id', '=', $variable)
					->wherenull('stat_user_views.deleted_at')
					;
			})
			->orderby('views', 'desc')
		;
	}
}