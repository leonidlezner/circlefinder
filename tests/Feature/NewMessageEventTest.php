<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Traits\UsersAdmins;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

/**
 * @group message_events
 */
class NewMessageEventTest extends TestCase
{
    use DatabaseMigrations;
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
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);
        $membership = $circle->joinWithDefaults($user2);

        $message = $circle->storeMessage($user, $faker->text, true);

        Notification::assertSentTo(
            $circle->users->filter(function ($value, $key) use ($user) {
                return $value->id != $user->id;
            }),
            \App\Notifications\NewMessageInCircle::class
        );
    }
}
