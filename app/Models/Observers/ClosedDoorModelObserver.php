<?php 

namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

/**
 * Used in BaseModel model
 *
 * @author cmooy
 */
class ClosedDoorModelObserver 
{
	public function creating($model)
	{
		return $this->is_allowed($model);
	}

	public function created($model)
	{
		return $this->is_allowed($model);
	}

	public function saving($model)
	{
		return $this->is_allowed($model);
	}

	public function saved($model)
	{
		return $this->is_allowed($model);
	}

	public function updating($model)
	{
		return $this->is_allowed($model);
	}

	public function updated($model)
	{
		return $this->is_allowed($model);
	}
	
	public function deleting($model)
	{
		return $this->is_allowed($model);
	}

	public function deleted($model)
	{
		return $this->is_allowed($model);
	}

	public function is_allowed($model)
	{
		$classes  = debug_backtrace();

		foreach ($classes as $key => $value) 
		{
		if(isset($value['class']) && str_is('App\Services\*', $value['class']))
			{
				return true;
			}
		}

		$model['errors'] = ['Tidak bisa diakses dari luar business workflow'];

		return false;
	}
}
