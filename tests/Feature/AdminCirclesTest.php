<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\UsersAdmins;

/**
 * @group admin.circles
 */
class AdminCirclesTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testGuestCannotAccessCircles()
    {
        $response = $this->get(route('admin.circles.index'));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');

        $response = $this->get(route('admin.circles.show', ['id' => '1234']));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    public function testAdminCanListCircles()
    {
        $admin = $this->fetchAdmin();
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.circles.index'));

        $response->assertStatus(200);
    }

    public function testAdminCanShowCircle()
    {
        $admin = $this->fetchAdmin();
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.circles.show', ['id' => $circle->id]));

        $response->assertStatus(200);
    }
}
