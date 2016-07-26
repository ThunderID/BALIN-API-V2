<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Models\PointLog;
use App\Models\StoreSetting;
use App\Models\Voucher;
use App\Models\Referral;
use App\Events\AuditStore;

/**
 * Used in PointLog model
 *
 * @author cmooy
 */
class PointLogObserver 
{
    /** 
     * observe Point Log event created
     * 1. Audit
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function created($model)
    {
        $errors                             = new MessageBag();

        //1. audit
        if($model->user()->count())
        {
            event(new AuditStore($model, 'point_added', 'Penambahan point user '.$model->user->name));
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    /** 
     * observe point log event saving
     * 1. Check if reference were from user
     * 2. Check if reference were from voucher
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function saving($model)
    {
        $errors                         = new MessageBag();

        //1. Check if reference were from user
        if($model->reference_type=='App\Models\User')
        {
            //1a. Check referee
            $reference                  = PointLog::userid($model->reference_id)->referencetype('App\Models\User')->first();
            $user                       = PointLog::userid($model->user_id)->referencetype('App\Models\User')->first();
            $alreadyhasvoucher          = PointLog::userid($model->user_id)->referencetype('App\Models\Voucher')->first();

            //1ai. If user alerady has voucher or referral, refuse
            if($user || $alreadyhasvoucher)
            {
                $errors->add('PointLog', 'Maaf, Anda tidak dapat menambahkan referral code, karena anda sudah menggunakan referral code '.$model->reference->referral_code);
            }
            //1aii. If referal was referenced by user, refuse
            elseif($reference && $model->user_id == $reference->reference_id)
            {
                $errors->add('PointLog', 'Tidak dapat memakai referensi dari pemberi referens.');
            }
            //1aiii. If referal belongs to user, refuse
            elseif($model->user_id == $model->reference_id)
            {
                $errors->add('PointLog', 'Tidak memakai dapat referral code anda sebagai pemberi referens.');
            }
            //1aiv. If referal quota lower equal to 0, refuse
            elseif($model->reference->quota_referral <= 0)
            {
                $errors->add('PointLog', 'Untuk saat ini tidak dapat menggunakan referral code '.$model->reference->name);
            }
            else
            {
                $gift                           = StoreSetting::type('invitation_royalty')->Ondate('now')->first();

                //check if no royalti, refuse
                if(!$gift)
                {
                    $errors->add('PointLog', 'Tidak ada campaign untuk point reference.');
                }
                //check if there were referral setting, let it be
                elseif($model->reference->referral->value!=0)
                {
                    $model->amount          = $model->reference->referral->value;
                    $model->notes           = 'Referensi promo '.$model->reference->name;
                }
                //check if there were no referral setting, let it be gift
                else
                {
                    $model->amount          = $gift->value;
                    $model->notes           = 'Direferensikan '.$model->reference->name;
                }
            }

            if(!$errors->count())
            {
                $result                     = $model->CreditQuota($model->reference->referral, 'Mereferensikan '.$model->user->name);
                if(!$result)
                {
                    return false;
                }
            }

        }
        //2. Check if reference were from voucher
        elseif($model->reference_type=='App\Models\Voucher')
        {
            $reference                      = PointLog::userid($model->reference->user_id)->referencetype('App\Models\User')->first();
            $user                           = PointLog::userid($model->reference->user_id)->referencetype('App\Models\User')->first();
            $alreadyhasvoucher              = PointLog::userid($model->user_id)->referencetype('App\Models\Voucher')->first();
            $alreadyhasreferral             = PointLog::userid($model->user_id)->referencetype('App\Models\User')->first();

            //2bi. If user alerady has voucher or referral, refuse
            if($alreadyhasvoucher || $alreadyhasreferral)
            {
                $errors->add('PointLog', 'Maaf, Anda tidak dapat menambahkan referral code, karena anda sudah menggunakan referral code '.$model->reference->referral_code);
            }
            //2aii. If referal was referenced by user, refuse
            elseif($reference && $model->user_id == $reference->reference_id)
            {
                $errors->add('PointLog', 'Tidak dapat memakai referensi dari pemberi referens.');
            }
            //2aiii. If referal belongs to user, refuse
            elseif($model->user_id == $model->reference->user_id)
            {
                $errors->add('PointLog', 'Tidak memakai dapat referral code anda sebagai pemberi referens.');
            }
            //2aiv. If referal quota lower equal to 0, refuse
            elseif($model->reference->quota <= 0)
            {
                $errors->add('PointLog', 'Untuk saat ini tidak dapat menggunakan referral code '.$model->reference->name);
            }
            else
            {
                $prev_reference         = $model->reference_id;

                $gift                   = StoreSetting::type('invitation_royalty')->Ondate('now')->first();

                //check if no royalti, refuse
                if(!$gift)
                {
                    $errors->add('PointLog', 'Tidak ada campaign untuk point reference.');
                }
                //check if there were referral setting, let it be
                elseif($model->reference->value!=0)
                {
                    $model->amount = $model->reference->value;
                    $model->notes  = 'Referensi promo #'.$model->reference->code;
                }
                //check if there were no referral setting, let it be gift
                else
                {
                    $model->amount = $gift->value;
                    $model->notes  = 'Direferensikan '.$model->reference->user->name;
                }
                
                if(!$errors->count())
                {
                    $result                     = $model->CreditQuota(\App\Models\Voucher::findorfail($prev_reference), 'Mereferensikan '.$model->user->name);
                    if(!$result)
                    {
                        return false;
                    }
                }
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
     * observe point log event saved
     * 1. Check if reference were from user
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function saved($model)
    {
        $errors                         = new MessageBag();

        //1. Check if reference were from user
        if($model->reference_type=='App\Models\User')
        {
            $gift                       = StoreSetting::type('referral_royalty')->Ondate('now')->first();

            //check if no royalti, refuse
            if(!$gift)
            {
                $errors->add('PointLog', 'Tidak ada campaign untuk point reference.');
            }
            else
            {
                //give royalti to referral
                $voucher                = Referral::userid($model->reference_id)->first();

                if($voucher && $voucher['value']==0)
                {
                    $referee            = new PointLog;

                    $referee->fill([
                            'user_id'       => $model->reference_id,
                            'amount'        => $gift->value,
                            'expired_at'    => $model->expired_at,
                            'notes'         => 'Mereferensikan '.$model->user->name,
                        ]);

                    $referee->reference()->associate($model);

                    if(!$referee->save())
                    {
                        $errors->add('PointLog', $referee->getError());
                    }
                }
            }
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }
}
