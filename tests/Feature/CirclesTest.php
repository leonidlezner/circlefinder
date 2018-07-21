<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Traits\UsersAdmins;
use Illuminate\Support\Facades\Artisan;

/**
 * @group circles
 */
class CirclesTest extends TestCase
{
    use DatabaseMigrations;
    use UsersAdmins;

    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed', ['--class' => 'LanguagesTableSeeder', '--env' => 'testing']);
    }

    public function testGuestCannotAccessCircle()
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

    public function testCanListCircles()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user)->get(route('circles.index'));

        $response->assertStatus(200);
    }

    public function testUserCanShowCircle()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user)->get(route('circles.show', ['uuid' => $circle->uuid]));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get(route('circles.show', ['uuid' => $faker->uuid]));
        $response->assertStatus(404);
    }

    public function testUserCannotGetPostRoute()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $response = $this->actingAs($user)->get(route('circles.update', ['uuid' => $faker->uuid]));
        
        $response->assertStatus(405);
    }

    public function testUserCanCreateCircle()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $response = $this->actingAs($user)->get(route('circles.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->post(route('circles.store'), [
            'type' => $faker->randomElement(config('circle.defaults.types')),
            'title' =>  $faker->catchPhrase,
            'begin' => today(),
            'languages' => [
                '0' => \App\Language::find(1)->code,
                '1' => \App\Language::find(2)->code,
                '2' => \App\Language::find(3)->code
            ],
            'limit' => 7,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $circle = $user->circles()->first();

        $this->assertTrue($circle->joined($user));

        $this->assertEquals(3, count($circle->languages));
        $this->assertEquals(7, $circle->limit);
    }

    public function testModeratorDoesNotAutojoinCircle()
    {
        $user = $this->fetchModerator();
        $faker = $this->fetchFaker();

        $response = $this->actingAs($user)->get(route('circles.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->post(route('circles.store'), [
            'type' => $faker->randomElement(config('circle.defaults.types')),
            'title' =>  $faker->catchPhrase,
            'begin' => today(),
            'languages' => [
                '0' => \App\Language::find(1)->code
            ],
            'limit' => 5
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $circle = $user->circles()->first();

        $this->assertFalse($circle->joined($user));
    }

    public function testSomeUserCannotEditCircle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user2)->get(route('circles.edit', ['uuid' => $circle->uuid]));

        $response->assertStatus(403);
    }

    public function testModeratorCanEditCircle()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);
        $user2 = $this->fetchModerator();
        
        $response = $this->actingAs($user2)->get(route('circles.edit', ['uuid' => $circle->uuid]));
        $response->assertStatus(200);
    }

    public function testOwnerCanEditCircle()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user)->get(route('circles.edit', ['uuid' => $circle->uuid]));
        $response->assertStatus(200);
    }

    public function testOwnerCanUpdateCircle()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user)->put(route('circles.update', ['uuid' => $circle->uuid]), [
            'type' => 'any',
            'title' =>  $faker->catchPhrase,
            'description' => $faker->text,
            'begin' => today(),
            'languages' => [
                '0' => \App\Language::find(1)->code,
                '1' => \App\Language::find(2)->code,
                '2' => \App\Language::find(3)->code
            ],
            'limit' => 5,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertEquals(3, count($circle->languages));

        $response = $this->actingAs($user)->put(route('circles.update', ['uuid' => $circle->uuid]), [
            'type' => 'any',
            'title' =>  $faker->catchPhrase,
            'description' => $faker->text,
            'begin' => today(),
            'languages' => [
                '0' => \App\Language::find(1)->code
            ],
            'limit' => 9,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $circle = $circle->refresh();

        $this->assertEquals(1, count($circle->languages));
        $this->assertEquals(9, $circle->limit);
    }

    public function testOwnerCanCompleteCircle()
    {
        $user = $this->fetchUser();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user)->post(route('circles.complete', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertTrue($circle->refresh()->completed);
    }

    public function testUserCannotCompleteCircle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user2)->post(route('circles.complete', ['uuid' => $circle->uuid]));

        $response->assertStatus(403);

        $this->assertFalse($circle->refresh()->completed);
    }

    public function testOwnerCanUncompleteCircle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $circle = $this->fetchCircle($user);

        $circle->complete();

        $response = $this->actingAs($user)->post(route('circles.uncomplete', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertFalse($circle->refresh()->completed);
    }

    public function testUserCannotJoinFullCircle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        for ($i = 0; $i < $circle->limit; $i++) {
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

    public function testUserCannotJoinCompletedCircle()
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

    public function testUserCanJoinAndLeaveCircle()
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

    public function testUserCannotDeleteCircle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($user2)->delete(route('circles.destroy', ['uuid' => $circle->uuid]));

        $response->assertStatus(403);

        $this->assertDatabaseHas('circles', [
            'id' => $circle->id
        ]);
    }

    public function testOwnerCanDeleteCircle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $user3 = $this->fetchUser();

        $faker = $this->fetchFaker();
        
        $circle = $this->fetchCircle($user);

        $circle->joinWithDefaults($user2);

        $response = $this->actingAs($user)->delete(route('circles.destroy', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);

        $circle->leave($user2);

        $response = $this->actingAs($user)->delete(route('circles.destroy', ['uuid' => $circle->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }

    public function testModeratorCanRemoveUsersFromCircle()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $moderator = $this->fetchModerator();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $circle->joinWithDefaults($user2);

        $response = $this->actingAs($user)
                ->delete(route('circles.remove', ['uuid' => $circle->uuid, 'user_uuid' => $user2->uuid]));

        $response->assertStatus(403);

        $response = $this->actingAs($moderator)
        ->delete(route('circles.remove', ['uuid' => $circle->uuid, 'user_uuid' => $user2->uuid]));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('memberships', [
            'circle_id' => $circle->id,
            'user_id' => $user2->id
        ]);
    }
}
