<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\UsersAdmins;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

/**
 * @group circle_events
 */
class CircleCompletedEventTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testCircleCompletedEvent()
    {
        Event::fake();

        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);
        $circle->complete();

        Event::assertDispatched(\App\Events\CircleCompleted::class, function ($e) use ($circle) {
            return ($e->circle == $circle);
        });
    }

    public function testCircleCompletedNotification()
    {
        Notification::fake();
        
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);
        
        $circle->joinWithDefaults($user);
        $circle->joinWithDefaults($user2);

        $circle->complete();

        Notification::assertSentTo(
            [$user, $user2],
            \App\Notifications\CircleCompleted::class
        );
    }
}
