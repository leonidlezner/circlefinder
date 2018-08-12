<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\UsersAdmins;
use Illuminate\Support\Facades\Artisan;

/**
 * @group messages
 */
class MessagesTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testGuestCannotAccessMembership()
    {
        $response = $this->post(route('circles.messages.store', ['circle_uuid' => '1234']));
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function testUserCanPostMessage()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $user3 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $text = $faker->text;

        $response = $this->actingAs($user2)->post(route('circles.messages.store', ['circle_uuid' => $circle->uuid]), [
            'body' => $text
        ]);

        $response->assertStatus(403);

        $circle->joinWithDefaults($user);
        $circle->joinWithDefaults($user2);

        $response = $this->actingAs($user2)->post(route('circles.messages.store', ['circle_uuid' => $circle->uuid]), [
            'body' => $text,
            'show_to_all' => false,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('messages', [
            'user_id' => $user2->id,
            'circle_id' => $circle->id,
        ]);

        $response = $this->actingAs($user3)->post(route('circles.messages.store', ['circle_uuid' => $circle->uuid]), [
            'body' => $text
        ]);

        $response->assertStatus(403);
    }

    public function testUserCanUpdateMessage()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $user3 = $this->fetchUser();
        $faker = $this->fetchFaker();
        
        $circle = $this->fetchCircle($user);
        
        $circle->joinWithDefaults($user);

        $message = $this->fetchMessage($circle, $user);
        
        $text = $faker->text;

        $response = $this->actingAs($user2)->put(route('circles.messages.update', [
            'circle_uuid' => $circle->uuid,
            'uuid' => $message->uuid
        ]), [
            'body' => $text,
            'show_to_all' => false,
        ]);

        $response->assertStatus(403);

        $response = $this->actingAs($user)->put(route('circles.messages.update', [
            'circle_uuid' => $circle->uuid,
            'uuid' => $message->uuid
        ]), [
            'body' => $text,
            'show_to_all' => false,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }

    public function testUserCanDeleteMessage()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $user3 = $this->fetchUser();
        $faker = $this->fetchFaker();
        
        $circle = $this->fetchCircle($user);
        
        $circle->joinWithDefaults($user);

        $message = $this->fetchMessage($circle, $user);

        $response = $this->actingAs($user2)->delete(route('circles.messages.destroy', [
            'circle_uuid' => $circle->uuid,
            'uuid' => $message->uuid
        ]));

        $response->assertStatus(403);

        $this->assertDatabaseHas('messages', [
            'uuid' => $message->uuid,
        ]);

        $response = $this->actingAs($user)->delete(route('circles.messages.destroy', [
            'circle_uuid' => $circle->uuid,
            'uuid' => $message->uuid
        ]));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('messages', [
            'uuid' => $message->uuid,
            'deleted_at' => null,
        ]);
    }
}
