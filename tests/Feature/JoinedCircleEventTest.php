<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\UsersAdmins;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

/**
 * @group circle_events
 */
class JoinedCircleEventTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testUserJoinedCircleEvent()
    {
        Event::fake();

        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);
        $membership = $circle->joinWithDefaults($user2);

        Event::assertDispatched(\App\Events\UserJoinedCircle::class, function ($e) use ($circle, $user2, $membership) {
            return ($e->circle == $circle) && ($e->user == $user2) && ($e->membership == $membership);
        });
    }

    public function testUserJoinedCircleNotification()
    {
        Notification::fake();
        
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);
        $membership = $circle->joinWithDefaults($user2);

        Notification::assertSentTo(
            [$user],
            \App\Notifications\UserJoinedCircle::class
        );
    }

    public function testOwnerJoinedCircleNoNotification()
    {
        Notification::fake();
        
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);
        $membership = $circle->joinWithDefaults($user);

        Notification::assertNotSentTo(
            [$user],
            \App\Notifications\UserJoinedCircle::class
        );
    }
}
