<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Traits\UsersAdmins;

/**
 * @group home
 */
class HomeTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testGuestCanAccessHome()
    {
        $response = $this->get(route('index'));

        $response->assertStatus(200);
    }

    public function testUserRedirectedToCircles()
    {
        $user = $this->fetchUser();

        $response = $this->actingAs($user)->get(route('index'));

        $response->assertStatus(302);

        $response->assertRedirect(route('circles.index'));
    }

    public function testUserCanAccessHome()
    {
        $user = $this->fetchUser();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
    }
}
