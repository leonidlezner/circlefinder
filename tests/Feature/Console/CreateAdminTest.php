<?php

namespace Tests\Feature\Console;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class CreateAdminTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * @group console
     *
     */
    public function testCheckCreateAdminShortPassword()
    {
        $command = Artisan::call('admin:create', [
            'name' => 'value1',
            'email' => 'value2',
            'pass' => "abcde"
        ]);

        $resultAsText = Artisan::output();

        $this->assertEquals($resultAsText, "The password shall be at least 6 charachters long!\n");
    }

    /**
     * @group console
     *
     */
    public function testCheckCreateAdmin()
    {
        $command = Artisan::call('admin:create', [
            'name' => 'Max Power',
            'email' => 'max@pow.er',
            'pass' => "abcdef"
        ]);

        $resultAsText = Artisan::output();

        $this->assertEquals($resultAsText, "New admin created!\n");

        $this->assertDatabaseHas('admins', [
            'email' => 'max@pow.er'
        ]);
    }

    /**
     * @group console
     *
     */
    public function testCheckCreateAdminAlreadyExists()
    {
        $admin = factory(\App\Admin::class)->create();

        $command = Artisan::call('admin:create', [
            'name' => 'Max Power',
            'email' => $admin->email,
            'pass' => "abcdef"
        ]);

        $resultAsText = Artisan::output();

        $this->assertEquals($resultAsText, "Admin with this email is already in the database!\n");
    }
}
