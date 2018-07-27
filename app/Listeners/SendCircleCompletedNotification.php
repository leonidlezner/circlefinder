<?php

namespace App\Listeners;

use App\Events\CircleCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendCircleCompletedNotification
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
     * @param  CircleCompleted  $event
     * @return void
     */
    public function handle(CircleCompleted $event)
    {
        $circle = $event->circle;
        $users = $event->circle->users;

        if ($users->isEmpty()) {
            return;
        }

        \Log::info(sprintf(
            'Sending CircleCompleted notification to %s for %s',
            $users->implode('name', ', '),
            (string) $event->circle
        ));

        Notification::send(
            $users,
            new \App\Notifications\CircleCompleted($event->circle)
        );
    }
}
