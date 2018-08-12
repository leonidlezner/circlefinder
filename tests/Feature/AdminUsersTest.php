<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tests\Traits\UsersAdmins;

/**
 * @group users
 */
class AdminUsersTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testGuestCannotAcessUsers()
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');

        $response = $this->get(route('admin.users.show', ['id' => 5]));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');

        $response = $this->get(route('admin.users.create'));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');

        $response = $this->get(route('admin.users.edit', ['id' => 5]));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');

        $response = $this->post(route('admin.users.store'));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');

        $response = $this->put(route('admin.users.update', ['id' => 5]));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');

        $response = $this->delete(route('admin.users.destroy', ['id' => 5]));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    public function testGetUsersIndex()
    {
        $admin = $this->fetchAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    public function testGetUsersTrash()
    {
        $admin = $this->fetchAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.trash'));

        $response->assertStatus(200);
    }

    public function testCreateUser()
    {
        $admin = $this->fetchAdmin();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.users.create'));

        $response->assertStatus(200);
    }

    public function testEditUser()
    {
        $admin = $this->fetchAdmin();
        $user = $this->fetchUser();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.users.edit', ['id' => 5]));

        $response->assertStatus(404);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.users.edit', ['id' => $user->id]));

        $response->assertStatus(200);
    }

    public function testStoreUser()
    {
        $admin = $this->fetchAdmin();
        $faker = $this->fetchFaker();

        $new_mail = $faker->unique()->safeEmail;
        $new_password = str_random(10);
        $new_name = $faker->name;

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.users.store'), [
                'name' => $new_name,
                'email' => $new_mail,
                'password' => $new_password,
                'timezone' => $faker->timezone,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $user = \App\User::where('email', $new_mail)->get()->first();

        $this->assertEquals($user->name, $new_name);
        $this->assertEquals($user->email, $new_mail);
        $this->assertTrue(Hash::check($new_password, $user->password));
    }

    public function testStoreUserNoPassword()
    {
        $admin = $this->fetchAdmin();
        $faker = $this->fetchFaker();

        $new_mail = $faker->unique()->safeEmail;
        $new_name = $faker->name;

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.users.store'), [
                'name' => $new_name,
                'email' => $new_mail,
                'timezone' => $faker->timezone
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }

    public function testUpdateUser()
    {
        $admin = $this->fetchAdmin();
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $new_mail = $faker->unique()->safeEmail;
        $new_password = str_random(10);
        $new_name = $faker->name;

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.users.update', ['id' => $user->id]), [
                'name' => $new_name,
                'email' => $new_mail,
                'password' => $new_password,
                'timezone' => $faker->timezone,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $user = $this->fetchUser($user->id);

        $this->assertEquals($user->name, $new_name);
        $this->assertEquals($user->email, $new_mail);
        $this->assertTrue(Hash::check($new_password, $user->password));
    }

    public function testUpdateUserSameMail()
    {
        $admin = $this->fetchAdmin();
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $new_name = $faker->name;

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.users.update', ['id' => $user->id]), [
                'name' => $new_name,
                'email' => $user->email,
                'timezone' => $faker->timezone,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $user = $this->fetchUser($user->id);

        $this->assertEquals($user->name, $new_name);
    }

    public function testUpdateUserNoNewPass()
    {
        $admin = $this->fetchAdmin();
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $new_mail = $faker->unique()->safeEmail;
        $new_name = $faker->name;

        $old_password = $user->password;

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.users.update', ['id' => $user->id]), [
                'name' => $new_name,
                'email' => $new_mail,
                'timezone' => $faker->timezone,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $user = $this->fetchUser($user->id);

        $this->assertEquals($old_password, $user->password);
    }

    public function testSoftDeleteUser()
    {
        $admin = $this->fetchAdmin();
        $user = $this->fetchUser();

        $response = $this->actingAs($admin, 'admin')
            ->delete(route('admin.users.destroy', ['id' => $user->id]));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('users', [
            'email' => $user->email,
            'name' => $user->name,
            'password' => $user->password,
        ]);

        $user = $this->fetchUser($user->id);
        $this->assertTrue(is_null($user));
    }

    public function testSoftRestoreUser()
    {
        $admin = $this->fetchAdmin();
        $user = $this->fetchUser();

        $user->delete();

        $this->assertSoftDeleted('users', [
            'email' => $user->email,
            'name' => $user->name,
            'password' => $user->password,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.users.restore', ['id' => $user->id]));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $restored_user = $this->fetchUser($user->id);

        $this->assertEquals($restored_user->email, $user->email);
    }

    public function testForceDeleteUser()
    {
        $admin = $this->fetchAdmin();
        $user = $this->fetchUser();

        $user->delete();

        $this->assertSoftDeleted('users', [
            'email' => $user->email,
            'name' => $user->name,
            'password' => $user->password,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->delete(route('admin.users.forcedelete', ['id' => $user->id]));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $restored_user = \App\User::withTrashed()->find($user->id);

        $this->assertTrue(is_null($restored_user));
    }

    public function testUpdateUserRoles()
    {
        $admin = $this->fetchAdmin();
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $faker->seed(1234);

        $roles = factory(\App\Role::class, 10)->create();

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.users.update', ['id' => $user->id]), [
                'name' => $user->name,
                'email' => $user->email,
                'timezone' => $faker->timezone,
                'roles' => [
                    '4' => $roles[0]->id,
                    '6' => $roles[1]->id,
                    '8' => $roles[2]->id,
                    '9' => $roles[3]->id,
                ],
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertEquals(4, count($user->roles));
    }

    public function testDetachUserRoles()
    {
        $admin = $this->fetchAdmin();
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $roles = factory(\App\Role::class, 10)->create();

        $user->roles()->attach($roles);

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.users.update', ['id' => $user->id]), [
                'name' => $user->name,
                'email' => $user->email,
                'timezone' => $faker->timezone,
                'roles' => [
                ],
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertEquals(0, count($user->roles));
    }

    public function testWrongUserRoles()
    {
        $admin = $this->fetchAdmin();
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.users.update', ['id' => $user->id]), [
                'name' => $user->name,
                'email' => $user->email,
                'timezone' => $faker->timezone,
                'roles' => [
                    '9' => 100,
                ],
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }
}
