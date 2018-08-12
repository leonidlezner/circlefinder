<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use Tests\Browser\Traits\CleanCookies;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use RefreshDatabase;
    use CleanCookies;

    public function testUserCanRegister()
    {
        $user_data = [
            'name' => 'dont care',
            'email' => 'test@test.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ];

        $this->browse(function (Browser $browser) use ($user_data) {
            $browser->visit('/register')
                ->type('name', $user_data['name'])
                ->type('email', $user_data['email'])
                ->type('password', $user_data['password'])
                ->type('password_confirmation', $user_data['password_confirmation'])
                ->press('Register')
                ->assertPathIs(config('auth.login_redirect'));

            $this->assertDatabaseHas('users', [
                'email' => $user_data['email'],
            ]);

            $browser->visit('/logout')->assertPathIs('/');

            $this->assertGuest();
        });
    }

    public function testUserCanLogin()
    {
        $user = factory(\App\User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'secret')
                ->press('Login')
                ->assertPathIs(config('auth.login_redirect'));
        });
    }
}
