<?php

namespace App\Listeners;

use \App\Events\UserJoinedCircle;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNewMemberNotification
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
     * @param  UserJoinedCircle  $event
     * @return void
     */
    public function handle(UserJoinedCircle $event)
    {
        dd($event);
    }
}
