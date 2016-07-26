<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs to cluster.
 *
 * @author cmooy
 */
trait HasClusterTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasClusterTraitConstructor()
	{
		//
	}

	/**
	 * call belongsto relationship with cluster
	 *
	 **/
	public function Cluster()
	{
		return $this->belongsTo('App\Models\Cluster', 'category_id');
	}
	
	/**
	 * check if model has cluster
	 *
	 **/
	public function scopeHasCluster($query, $variable)
	{
		return $query->whereHas('cluster', function($q)use($variable){$q;});
	}

	/**
	 * check if model has cluster in certain id
	 *
	 * @var singular id
	 **/
	public function scopeClusterID($query, $variable)
	{
		return $query->whereHas('cluster', function($q)use($variable){$q->id($variable);});
	}

	/**
	 * call belongsto relationship with category
	 *
	 **/
	public function Category()
	{
		return $this->belongsTo('App\Models\Category', 'category_id');
	}

	/**
	 * call belongsto relationship with tag
	 *
	 **/
	public function Tag()
	{
		return $this->belongsTo('App\Models\Tag', 'category_id');
	}
}