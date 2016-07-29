<?php 

namespace App\Entities\TraitRelations;

/**
 * Trait for models belongs to CategoryCluster.
 *
 * @author cmooy
 */
trait BelongsToCategoryClusterTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function BelongsToCategoryClusterTraitConstructor()
	{
		//
	}

	/**
	 * call belongsto relationship with CategoryCluster
	 *
	 **/
	public function CategoryCluster()
	{
		return $this->belongsTo('App\Entities\CategoryCluster', 'category_id');
	}
	
	/**
	 * check if model has CategoryCluster
	 *
	 **/
	public function scopeBelongsToCategoryCluster($query, $variable)
	{
		return $query->whereHas('CategoryCluster', function($q)use($variable){$q;});
	}

	/**
	 * check if model has CategoryCluster in certain id
	 *
	 * @var singular id
	 **/
	public function scopeCategoryClusterID($query, $variable)
	{
		return $query->whereHas('CategoryCluster', function($q)use($variable){$q->id($variable);});
	}

	/**
	 * call belongsto relationship with category
	 *
	 **/
	public function Category()
	{
		return $this->belongsTo('App\Entities\Category', 'category_id');
	}

	/**
	 * call belongsto relationship with tag
	 *
	 **/
	public function Tag()
	{
		return $this->belongsTo('App\Entities\Tag', 'category_id');
	}
}