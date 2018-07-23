<?php

namespace App\Listeners;

use \App\Events\UserJoinedCircle;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

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
        $users = [
            $event->circle->user,
        ];

        \Log::info(sprintf(
            'Sending UserJoinedCircle notification to %s. Member "%s" joined "%s"',
            implode(',', $users),
            $event->user->name,
            (string) $event->circle
        ));

        Notification::send($users, new \App\Notifications\UserJoinedCircle($event->circle));
    }
}
