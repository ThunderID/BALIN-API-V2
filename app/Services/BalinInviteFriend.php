<?php

namespace App\Services;

use Illuminate\Support\MessageBag;

use App\Contracts\InviteFriendInterface;

use App\Contracts\Policies\ValidatingReferralSistemInterface;
use App\Contracts\Policies\ProceedReferralSistemInterface;
use App\Contracts\Policies\EffectReferralSistemInterface;

use App\Entities\Customer;
use App\Entities\UserInvitationLog;

class BalinInviteFriend implements InviteFriendInterface 
{
	protected $customer;
	protected $errors;
	protected $saved_data;
	protected $pre;
	protected $post;
	protected $pro;

	/**
	 * construct function, iniate error
	 *
	 */
	function __construct(ValidatingReferralSistemInterface $pre, ProceedReferralSistemInterface $pro, EffectReferralSistemInterface $post)
	{
		$this->errors 	= new MessageBag;
		$this->pre 		= $pre;
		$this->pro 		= $pro;
		$this->post 	= $post;
	}

	/**
	 * return errors
	 *
	 * @return MessageBag
	 **/
	function getError()
	{
		return $this->errors;
	}

	/**
	 * return saved_data
	 *
	 * @return saved_data
	 **/
	function getData()
	{
		return $this->saved_data;
	}

	/**
	 * Checkout
	 *
	 * 1. Call Class fill
	 * 
	 * @return Response
	 */
	public function fill(array $customer)
	{
		$this->customer 		= $customer;
	}

	/**
	 * Save
	 *
	 * Here's the workflow
	 * 
	 * @return Response
	 */
	public function save()
	{
		$customer 			= Customer::find($this->customer['id']);

		foreach ($this->customer['friends'] as $key => $value) 
		{
			$invite 		= UserInvitationLog::userid($customer['id'])->email($value)->first();

			if(!$invite)
			{
				$invite 	= new UserInvitationLog;
			}

			$invite->fill(['user_id' => $customer['id'], 'email' => $value, 'is_used' => false, 'invitation_link' => $customer->code_referral.md5(uniqid(rand(), TRUE))]);

			if(!$invite->save())
			{
				$this->errors 		= $invite->getError();

				return false;
			}

			$this->post->sendinvitationmail($customer, $value, $invite->invitation_link);
		}

		//8. Return customer Model Object
		$this->saved_data	= $customer;

		return true;
	}
}
