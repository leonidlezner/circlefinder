<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\UsersAdmins;

/**
 * @group roleuser
 */
class RoleUserTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testAttachRoles()
    {
        $user = $this->fetchUser();
        $userB = $this->fetchUser();

        $roles = [];

        $roles[] = $this->fetchRole();
        $roles[] = $this->fetchRole();
        $roles[] = $this->fetchRole();

        $user->roles()->attach($roles[0]);
        $user->roles()->attach($roles[1]);
        $user->roles()->attach($roles[2]);

        $userB->roles()->attach($roles[2]);

        $this->assertEquals(count($roles), count($user->roles));

        $this->assertEquals(1, count($roles[0]->users));
        $this->assertEquals(2, count($roles[2]->users));
    }

    public function testDeleteRoles()
    {
        $user = $this->fetchUser();

        $roles = [];

        $roles[] = $this->fetchRole();
        $roles[] = $this->fetchRole();
        $roles[] = $this->fetchRole();

        $user->roles()->attach($roles[0]);
        $user->roles()->attach($roles[1]);
        $user->roles()->attach($roles[2]);

        $user->delete();
        $this->assertEquals(count($roles), count($user->roles));

        $this->assertDatabaseHas('role_user', [
            'role_id' => $roles[0]->id,
        ]);

        $this->assertEquals(0, count($roles[0]->users));

        $user->forceDelete();
        $this->assertDatabaseMissing('role_user', [
            'role_id' => $roles[0]->id,
        ]);
    }

    public function testCheckIfUserHasRole()
    {
        $user = $this->fetchUser();

        $roles = [];

        $roles[] = $this->fetchRole();
        $roles[] = $this->fetchRole();
        $roles[] = $this->fetchRole();
        $roles[] = $this->fetchRole();

        $user->roles()->attach($roles[0]);
        $user->roles()->attach($roles[1]);
        $user->roles()->attach($roles[2]);

        $this->assertTrue($user->hasRole($roles[0]->name));
        $this->assertTrue($user->hasRole($roles[1]->name));
        $this->assertTrue($user->hasRole($roles[2]->name));

        $this->assertFalse($user->hasRole($roles[3]->name));
    }
}
