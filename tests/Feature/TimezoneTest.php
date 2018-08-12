<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Traits\UsersAdmins;

/**
 * @group timezone
 */
class TimezoneTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testRedirectUserWithoutTimezone()
    {
        $user = $this->fetchUser();

        $faker = $this->fetchFaker();


        $user->timezone = null;
        $user->save();
        $user = $user->refresh();
        
        $response = $this->actingAs($user)->get(route('circles.index'));

        $response->assertSessionHasErrors();
        $response->assertStatus(302);
        $response->assertRedirect(route('profile.timezone.edit'));
    }

    public function testSetTimezone()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $user->timezone = null;
        $user->save();
        
        $response = $this->actingAs($user)->get(route('profile.timezone.edit'));
        $response->assertStatus(200);
        
        $response = $this->actingAs($user)->put(route('profile.timezone.update', [
            'timezone' => 'Europe/Berlin'
        ]));

        $response->assertSessionHas('success');
        $response->assertStatus(302);
        $response->assertRedirect(route('profile.show', ['uuid' => $user->uuid]));

        $user = $user->refresh();

        $this->assertEquals('Europe/Berlin', $user->timezone);
    }
}
