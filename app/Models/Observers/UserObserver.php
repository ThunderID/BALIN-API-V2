<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Hash;

use App\Models\User;

/**
 * Used in User, Customer, Admin Model
 *
 * @author cmooy
 */
class UserObserver 
{
    /** 
     * observe user event creating
     * 1. check is active
     * 2. generate activation link
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function creating($model)
    {
        $errors                             = new MessageBag();

        //1. check is active
        if(is_null($model->is_active))
        {
            $model->is_active               = false;
        }

        //2. activation link used to generate link for first claimed voucher
        if($model->is_active==false)
        {
            $model->activation_link         = md5(uniqid(rand(), TRUE));
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    /** 
     * observe user event created
     * 1. generate referral code
     * 2. give referral code and regist quota
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function created($model)
    {
        $errors                             = new MessageBag();

        //1. generate referral_code
        $referral_code                      = $model->generateReferralCode($model);
        
        //2. give referral code and regist quota
        $result                             = $model->giveReferralCode($model, $referral_code);

        if(!$result)
        {
            return false;
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    /** 
     * observe user event saving
     * 1. Check email
     * 2. Check rehash password
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function saving($model)
    {
        $errors                             = new MessageBag();

        if(is_null($model->id))
        {
            $id                             = 0;
        }
        else
        {
            $id                             = $model->id;
        }

        //1. Check email
        $user                               = User::email($model->email)->notid($id)->first();

        if($user)
        {
            $errors->add('User', 'Email sudah terdaftar.');
        }

        //2. Check rehash password
        if (Hash::needsRehash($model->password))
        {
            $model->password                = bcrypt($model->password);
            $model->reset_password_link     = '';
        }

        //3. Check is active
        if(isset($model->getDirty()['is_active']) && $model->is_active && !is_null($model->id))
        {
            $result                         = $model->giveWelcomeGift($model);

            if(!$result)
            {
                return false;
            }
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    /** 
     * observe user event saving
     * 1. Check transaction relationship
     * 2. Check point log relationship
     * 3. Check quota log relationship
     * 4. Check auditor relationship
     * 5. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function deleting($model)
    {
        $errors                             = new MessageBag();

        //1. Check transaction relationship
        if($model->transactions()->count())
        {
            $errors->add('User', 'Tidak bisa menghapus User yang telah bertransaksi.');
        }

        //2. Check point log relationship
        if($model->pointlogs()->count())
        {
            $errors->add('User', 'Tidak bisa menghapus User yang memiliki balin point.');
        }

        //3. Check quota log relationship
        if($model->quotalogs()->count())
        {
            $errors->add('User', 'Tidak bisa menghapus User yang memiliki quota.');
        }

        //4. Check auditor relationship
        if($model->auditors()->count())
        {
            $errors->add('User', 'Tidak bisa menghapus User yang terlibat dalam sistem audit.');
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }
}
