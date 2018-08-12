<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Traits\UsersAdmins;
use Socialite;
use Mockery;
use Illuminate\Auth\Events\PasswordReset;

/**
 * @group login
 */
class LoginRegisterTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testGuestSeesLoginForm()
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
    }

    public function testGuestHomeRedirectsToLogin()
    {
        $response = $this->get(route('home'));
        
        $response->assertStatus(302);

        $response->assertRedirect('/login');
    }

    public function testGuestCannotLogin()
    {
        $params = [
            'email' => 'fake@mail.boo',
            'password' => 'ABCDEFG123'
        ];

        $response = $this->post('/login', $params);

        $response->assertSessionHasErrors();

        $this->assertGuest();
    }

    public function testGuestCanRegister()
    {
        $user_data = [
            'name' => 'Test Testman',
            'email' => 'test@testing.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'timezone' => 'Europe/Berlin'
        ];

        $response = $this->post('/register', $user_data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'email' => $user_data['email'],
            'timezone' => 'Europe/Berlin'
        ]);

        $this->assertAuthenticated();

        $user = \App\User::where('email', $user_data['email'])->first();

        $this->assertAuthenticatedAs($user);
    }

    public function testGuestCannotRegisterIfRegistered()
    {
        $user = factory(\App\User::class)->create();

        $user_data = [
            'name' => 'dont care',
            'email' => $user->email,
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ];

        $response = $this->post('/register', $user_data);

        $response->assertStatus(302);

        $response->assertSessionHasErrors();
        
        $this->assertGuest();
    }

    public function testUserCanLogin()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret'
        ]);

        $response->assertStatus(302);

        $this->assertAuthenticatedAs($user);
    }

    public function testUserCannotLoginWrongPass()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(302);

        $this->assertGuest();
    }
    
    public function testUserCanLogout()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertStatus(302);
        
        $this->assertGuest();
    }

    public function testAdminLoginRedicrectsToDashboard()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->get('/login');
        
        $response->assertStatus(302);

        $response->assertRedirect(route('home'));
    }

    public function testGuestCanSeeResetPassword()
    {
        $response = $this->get('/password/reset');
        $response->assertStatus(200);

        $response = $this->get('/password/reset/abcdef');
        $response->assertStatus(200);
    }
    
    public function testRedirectToFacebook()
    {
        $response = $this->get('/login/facebook');
        $response->assertStatus(302);
    }

    public function testLoginWithFacebookNewUser()
    {
        $faker = $this->fetchFaker();

        $email = $faker->email;

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

        $abstractUser->shouldReceive('getId')
            ->andReturn($faker->randomNumber)
            ->shouldReceive('getEmail')
            ->andReturn($email)
            ->shouldReceive('getName')
            ->andReturn($faker->name);

        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $response = $this->get('/login/facebook/callback');
                
        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'email' => $email
        ]);
    }

    public function testLoginWithFacebookExistingMail()
    {
        $faker = $this->fetchFaker();
        $user = $this->fetchUser();

        $email = $user->email;
        $id = $faker->randomNumber;

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

        $abstractUser->shouldReceive('getId')
            ->andReturn($id)
            ->shouldReceive('getEmail')
            ->andReturn($email)
            ->shouldReceive('getName')
            ->andReturn($faker->name);

        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $response = $this->get('/login/facebook/callback');
        
        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'provider_id' => $id
        ]);
    }

    public function testLoginWithFacebookExistingId()
    {
        $faker = $this->fetchFaker();
        $user = $this->fetchUser();

        $old_email = $user->email;
        $email = $faker->email;
        $id = $faker->randomNumber;

        $user->provider_id = $id;
        $user->save();

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

        $abstractUser->shouldReceive('getId')
            ->andReturn($id)
            ->shouldReceive('getEmail')
            ->andReturn($email)
            ->shouldReceive('getName')
            ->andReturn($faker->name);
        
        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $response = $this->get('/login/facebook/callback');
        
        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'provider_id' => $id,
            'email' => $old_email
        ]);
    }

    public function testSetHasPasswordOnReset()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $user->no_password = true;
        $user->save();
        
        event(new PasswordReset($user));

        $this->assertFalse($user->no_password);
    }
}
