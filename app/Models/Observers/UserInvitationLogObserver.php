<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Models\UserInvitationLog;

/**
 * Used in User Invitation Log Model
 *
 * @author cmooy
 */
class UserInvitationLogObserver 
{
	/** 
	 * observe user invitation log event deleting
	 * 1. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function deleting($model)
	{
		$errors                             = new MessageBag();

		$errors->add('User', 'Tidak bisa menghapus log invitation.');

		if($errors->count())
		{
			$model['errors']                = $errors;

			return false;
		}

		return true;
	}
}
