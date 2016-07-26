<?php namespace App\Models\Traits\belongsToMany;

/**
 * Trait for models belongs to many Clusters.
 *
 * @author cmooy
 */
trait HasClustersTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasClustersTraitConstructor()
	{
		//
	}

	/**
	 * call belongsto many relationship clusters' type
	 *
	 **/
	public function Clusters()
	{
		return $this->belongsToMany('App\Models\Cluster', 'categories_products', 'product_id', 'category_id');
	}

	/**
	 * check if model has category in certain slug
	 *
	 * @var array or singular slug
	 **/
	public function scopeClustersSlug($query, $variable)
	{
		return $query->whereHas('clusters', function($q)use($variable){$q->slug($variable);});
	}
	/**
	 * call belongsto many relationship category's type
	 *
	 **/
	public function Categories()
	{
		return $this->belongsToMany('App\Models\Category', 'categories_products', 'product_id', 'category_id');
	}

	/**
	 * check if model has category in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeCategoriesID($query, $variable)
	{
		if(is_array($variable))
		{
			foreach ($variable as $key => $value) 
			{
				$query = $query->whereHas('categories', function($q)use($value){$q->id($value);});
			}

			return $query;
		}

		return $query->whereHas('categories', function($q)use($variable){$q->id($variable);});

		// return $query->whereHas('categories', function($q)use($variable){$q->id($variable);});
	}

	/**
	 * check if model has category in certain slug
	 *
	 * @var array or singular slug
	 **/
	public function scopeCategoriesSlug($query, $variable)
	{
		if(is_array($variable))
		{
			foreach ($variable as $key => $value) 
			{
				$query = $query->whereHas('categories', function($q)use($value){$q->slug($value);});
			}

			return $query;
		}

		return $query->whereHas('categories', function($q)use($variable){$q->slug($variable);});
	}

	/**
	 * call belongsto many relationship tag's type
	 *
	 **/
	public function Tags()
	{
		return $this->belongsToMany('App\Models\Tag', 'categories_products', 'product_id', 'category_id');
	}

	/**
	 * check if model has tag in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeTagsID($query, $variable)
	{
		if(is_array($variable))
		{
			foreach ($variable as $key => $value) 
			{
				$query = $query->whereHas('tags', function($q)use($value){$q->id($value);});
			}

			return $query;
		}

		return $query->whereHas('tags', function($q)use($variable){$q->id($variable);});


		// return $query->whereHas('tags', function($q)use($variable){$q->id($variable);});
	}

	/**
	 * check if model has tag in certain slug
	 *
	 * @var array or singular slug
	 **/
	public function scopeTagsSlug($query, $variable)
	{
		if(is_array($variable))
		{
			foreach ($variable as $key => $value) 
			{
				$query = $query->whereHas('tags', function($q)use($value){$q->slug($value);});
			}

			return $query;
		}

		return $query->whereHas('tags', function($q)use($variable){$q->slug($variable);});
	}
}