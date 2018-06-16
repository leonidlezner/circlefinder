<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\UsersAdmins;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;

/**
 * @group user
 */
class UserTest extends TestCase
{
    use DatabaseMigrations;
    use UsersAdmins;

    public function test_generate_uuid()
    {
        $faker = $this->fetchFaker();
        $user = new \App\User();

        $this->assertEquals(0, strlen($user->uuid));

        $user->name = $faker->name;
        $user->email = $faker->email;
        $user->password = Hash::make('secret');

        $user->save();

        $uuid = $user->uuid;

        $this->assertEquals(36, strlen($user->uuid));

        $user->name = $faker->name;

        $user->save();

        $this->assertEquals($uuid, $user->uuid);

        $u = \App\User::withUuid($uuid)->get()->first();

        $this->assertEquals($user->id, $u->id);
    }

    public function test_validation_rules()
    {
        $rules = \App\User::validationRules();
        $rules2 = \App\User::validationRules(['email']);

        $this->assertTrue(count($rules) > 0);

        $this->assertTrue(key_exists('email', $rules));
        
        $this->assertFalse(key_exists('email', $rules2));
    }

    public function test_get_new_avatar_filename()
    {
        $user = factory(\App\User::class)->create();

        $this->assertTrue(strlen($user->newAvatarFileName()) > 0);
    }
}
