<?php

namespace App\Listeners;

use App\Events\AuditStore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

/**
 * Listener for Audit store
 * 
 * @author cmooy
 */
class SaveAudit
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * 1. save audit
     *
     * @param  AuditStore  $event
     * @return void
     */
    public function handle(AuditStore $event)
    {
        //1. save audit
        $user                       = \LucaDegasperi\OAuth2Server\Facades\Authorizer::getResourceOwnerId();
        $user                       = json_decode($user, true)['data'];

        if($user)
        {
            $userid             = $user['id'];
        }
        else
        {
            $userid             = 0;
        }
        
        $audit                  = new \App\Models\Auditor;

        $audit->fill([
                'user_id'                       => $userid,
                'table_id'                      => $event->audit['id'],
                'table_type'                    => get_class($event->audit),
                'ondate'                        => Carbon::now()->format('Y-m-d H:i:s'),
                'type'                          => $event->type,
                'event'                         => $event->message,
            ]);        

        $audit->save();
        
        return true;
    }
}