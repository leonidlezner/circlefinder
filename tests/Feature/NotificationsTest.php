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
 * @group notifications
 */
class NotificationsTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testRedirectNotificationToCircle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);
        $membership = $circle->joinWithDefaults($user2);

        $notification = $user->unreadNotifications()->first();

        $this->assertFalse(is_null($notification));

        $response = $this->actingAs($user2)->get(route('notifications.show', ['id' => $notification->id]));
        $response->assertStatus(404);

        $response = $this->actingAs($user)->get(route('notifications.show', ['id' => $notification->id]));

        $response->assertStatus(302);
        $response->assertLocation(route('circles.show', ['uuid' => $circle->uuid]));

        $notification = $notification->refresh();

        $this->assertFalse(is_null($notification->read_at));
    }
}
