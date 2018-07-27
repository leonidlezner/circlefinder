<?php

namespace App\Listeners;

use App\Events\NewMessageInCircle;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendNewMessageNotification
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
     * @param  NewMessageInCircle  $event
     * @return void
     */
    public function handle(NewMessageInCircle $event)
    {
        $user = $event->user;

        $users = $event->circle->users->filter(function ($value, $key) use ($user) {
            return $value->id != $user->id;
        });
        
        if ($users->isEmpty()) {
            return;
        }

        \Log::info(sprintf(
            'Sending NewMessageInCircle notification to %s. Member %s commented %s',
            $users->implode('name', ', '),
            $event->user->name,
            (string) $event->circle
        ));

        Notification::send(
            $users,
            new \App\Notifications\NewMessageInCircle($event->circle, $event->user, $event->message)
        );
    }
}
