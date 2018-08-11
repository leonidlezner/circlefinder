<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use Tests\Browser\Traits\CleanCookies;
use Tests\DuskTestCase;

class AdminLoginTest extends DuskTestCase
{
    use RefreshDatabase;
    use CleanCookies;

    public function testAdminCanLogin()
    {
        $admin = factory(\App\Admin::class)->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit('/admin')
                ->type('email', $admin->email)
                ->type('password', 'secret')
                ->press('Login')
                ->assertPathIs("/admin");
        });
    }
}
