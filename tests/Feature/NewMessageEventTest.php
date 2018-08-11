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
 * @group message_events
 */
class NewMessageEventTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testUserPostedMessage()
    {
        Event::fake();

        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);
        $membership = $circle->joinWithDefaults($user2);

        $message = $circle->storeMessage($user, $faker->text, true);

        Event::assertDispatched(\App\Events\NewMessageInCircle::class, function ($e) use ($circle, $user, $message) {
            return ($e->circle == $circle) && ($e->user == $user) && ($e->message == $message);
        });
    }

    public function testUserPostedMessageNotification()
    {
        Notification::fake();
        
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $user3 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $circle->joinWithDefaults($user);
        $circle->joinWithDefaults($user2);
        $circle->joinWithDefaults($user3);

        $message = $circle->storeMessage($user, $faker->text, true);

        Notification::assertSentTo(
            [$user2, $user3],
            \App\Notifications\NewMessageInCircle::class
        );
    }
}
