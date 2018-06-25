<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Traits\UsersAdmins;


/**
 * @group circles
 */
class CirclesTest extends TestCase
{
    use DatabaseMigrations;
    use UsersAdmins;

    public function test_guest_cannot_access_circle()
    {
        $response = $this->get(route('circles.index'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $response = $this->get(route('circles.show', ['circle_uuid' => '1234']));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $response = $this->get(route('circles.edit', ['circle_uuid' => '1234']));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $response = $this->put(route('circles.update', ['circle_uuid' => '1234']));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $response = $this->delete(route('circles.destroy', ['circle_uuid' => '1234']));
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_can_list_circles()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user)->get(route('circles.index'));

        $response->assertStatus(200);
    }

    public function test_user_can_show_circle()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user)->get(route('circles.show', ['uuid' => $circle->uuid]));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get(route('circles.show', ['uuid' => $faker->uuid]));
        $response->assertStatus(404);
    }

    public function test_user_cannot_get_post_route()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $response = $this->actingAs($user)->get(route('circles.update', ['uuid' => $faker->uuid]));
        
        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    public function test_user_can_create_circle()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $response = $this->actingAs($user)->get(route('circles.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->post(route('circles.store'), [
            'type' => $faker->randomElement(config('circle.defaults.types')),
            'title' =>  $faker->catchPhrase,
            'limit' => config('circle.defaults.limit'),
            'begin' => today()
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }

    public function test_some_user_cannot_edit_circle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user2)->get(route('circles.edit', ['uuid' => $circle->uuid]));
        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    public function test_moderator_can_edit_circle()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);
        $user2 = $this->fetchModerator();
        
        $response = $this->actingAs($user2)->get(route('circles.edit', ['uuid' => $circle->uuid]));
        $response->assertStatus(200);
    }

    public function test_owner_can_edit_circle()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user)->get(route('circles.edit', ['uuid' => $circle->uuid]));
        $response->assertStatus(200);
    }

    public function test_owner_can_update_circle()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user)->put(route('circles.update', ['uuid' => $circle->uuid]), [
            'type' => 'any',
            'title' =>  $faker->catchPhrase,
            'description' => $faker->text,
            'begin' => today()
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }

    public function test_owner_can_complete_circle()
    {
        $user = $this->fetchUser();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user)->post(route('circles.complete', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertTrue($circle->refresh()->completed);
    }

    public function test_user_cannot_complete_circle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user2)->post(route('circles.complete', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $this->assertFalse($circle->refresh()->completed);
    }

    public function test_owner_can_uncomplete_circle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $circle = $this->fetchCircle($user);

        $circle->complete();

        $response = $this->actingAs($user2)->post(route('circles.uncomplete', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $this->assertTrue($circle->refresh()->completed);
    }

    public function test_user_cannot_join_full_circle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        for($i = 0; $i < $circle->limit; $i++)
        {
            $circle->joinWithDefaults($this->fetchUser());
        }

        $this->assertTrue($circle->completed);

        $response = $this->actingAs($user2)->post(route('circles.join', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $this->assertDatabaseMissing('memberships', [
            'circle_id' => $circle->id,
            'user_id' => $user2->id
        ]);
    }

    public function test_user_cannot_join_completed_circle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $circle->complete();

        $response = $this->actingAs($user2)->post(route('circles.join', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $this->assertDatabaseMissing('memberships', [
            'circle_id' => $circle->id,
            'user_id' => $user2->id
        ]);
    }

    public function test_user_can_join_and_leave_circle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user2)->post(route('circles.join', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('memberships', [
            'circle_id' => $circle->id,
            'user_id' => $user2->id
        ]);

        $response = $this->actingAs($user2)->post(route('circles.leave', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('memberships', [
            'circle_id' => $circle->id,
            'user_id' => $user2->id
        ]);
    }

    public function test_user_cannot_delete_circle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();        
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user2)->delete(route('circles.destroy', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('circles', [
            'id' => $circle->id
        ]);
    }

    public function test_owner_can_delete_circle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $user3 = $this->fetchUser();

        $faker = $this->fetchFaker();
        
        $circle = $this->fetchCircle($user);

        $circle->joinWithDefaults($user2);

        $response = $this->actingAs($user)->delete(route('circles.destroy', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors();

        $circle->leave($user2);

        $response = $this->actingAs($user)->delete(route('circles.destroy', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }
}
