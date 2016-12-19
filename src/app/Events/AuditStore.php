<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Event for Product Viewed
 * 
 * @author cmooy
 */
class AuditStore extends Event
{
    use SerializesModels;

    public $audit;

    public $type;

    public $message;

    /**
     * Create a new event instance.
     *
     * @param  model  $stat
     * @return void
     */
    public function __construct($audit, $type, $message)
    {
        $this->audit    = $audit;
        $this->type     = $type;
        $this->message  = $message;
    }
}