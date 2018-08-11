<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;


    public function testGuestSeesLoginForm()
    {
        $response = $this->get('/admin/login');
        
        $response->assertStatus(200);
    }

    public function testGuestCanNotLogin()
    {
        $response = $this->get('/admin');
        
        $response->assertStatus(302);

        $response->assertRedirect('/admin/login');
    }

    public function testAdminLoginRedicrectsToDashboard()
    {
        $admin = factory(\App\Admin::class)->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/login');
        
        $response->assertStatus(302);

        $response->assertRedirect('/admin');
    }
    
    public function testUserCanNotUseDashboard()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function testAdminCanLogin()
    {
        $admin = factory(\App\Admin::class)->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin');

        $response->assertStatus(200);
    }

    public function testAdminRedirectsToDashboard()
    {
        $admin = factory(\App\Admin::class)->create();

        $response = $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'secret'
        ]);

        $response->assertStatus(302);

        $response->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function testAdminCanLogout()
    {
        $admin = factory(\App\Admin::class)->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/logout');

        $response->assertRedirect('/admin/login');
    }

    public function testAdminCanPostLogout()
    {
        $admin = factory(\App\Admin::class)->create();

        $response = $this->actingAs($admin, 'admin')->post('/admin/logout');

        $response->assertRedirect('/admin/login');
    }

    public function testUserCannotLoginWrongPass()
    {
        $params = [
            'email' => 'fake@mail.boo',
            'password' => 'ABCDEFG123'
        ];

        $response = $this->post('/admin/login', $params);

        $response->assertSessionHasErrors();

        $this->assertGuest('admin');
    }
}
