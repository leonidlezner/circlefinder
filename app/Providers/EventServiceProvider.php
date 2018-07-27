<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\PasswordReset' => [
            'App\Listeners\PasswordResetListener',
        ],
        \App\Events\UserJoinedCircle::class => [
            \App\Listeners\SendNewMemberNotification::class
        ],
        \App\Events\NewMessageInCircle::class => [
            \App\Listeners\SendNewMessageNotification::class
        ],
        \App\Events\CircleCompleted::class => [
            \App\Listeners\SendCircleCompletedNotification::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
