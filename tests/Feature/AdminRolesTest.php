<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\UsersAdmins;

/**
 * @group roles
 */
class RolesTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function testGuestCannotAcessRoles()
    {
        $response = $this->get(route('admin.roles.index'));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');

        $response = $this->get(route('admin.roles.show', ['id' => 5]));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');

        $response = $this->get(route('admin.roles.create'));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');

        $response = $this->get(route('admin.roles.edit', ['id' => 5]));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');

        $response = $this->post(route('admin.roles.store'));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');

        $response = $this->put(route('admin.roles.update', ['id' => 5]));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');

        $response = $this->delete(route('admin.roles.destroy', ['id' => 5]));
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    public function testGetRolesIndex()
    {
        $admin = $this->fetchAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.roles.index'));

        $response->assertStatus(200);
    }

    public function testCreateRole()
    {
        $admin = $this->fetchAdmin();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.roles.create'));

        $response->assertStatus(200);
    }

    public function testShowRole()
    {
        $admin = $this->fetchAdmin();
        $role = $this->fetchRole();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.roles.show', ['id' => 5]));

        $response->assertStatus(404);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.roles.show', ['id' => $role->id]));

        $response->assertStatus(200);
    }

    public function testEditRole()
    {
        $admin = $this->fetchAdmin();
        $role = $this->fetchRole();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.roles.edit', ['id' => 5]));

        $response->assertStatus(404);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.roles.edit', ['id' => $role->id]));

        $response->assertStatus(200);
    }

    public function testStoreRole()
    {
        $admin = $this->fetchAdmin();
        $faker = $this->fetchFaker();

        $new_name = $faker->domainWord;
        $new_title = $faker->jobTitle;

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.roles.store'), [
                'name' => $new_name,
                'title' => $new_title,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $role = \App\Role::where('name', $new_name)->get()->first();

        $this->assertEquals($role->title, $new_title);
    }

    public function testUpdateRole()
    {
        $admin = $this->fetchAdmin();
        $role = $this->fetchRole();
        $faker = $this->fetchFaker();

        $new_name = $faker->domainWord;
        $new_title = $faker->jobTitle;

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.roles.update', ['id' => $role->id]), [
                'name' => $new_name,
                'title' => $new_title,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $role = $this->fetchRole($role->id);

        $this->assertEquals($role->name, $new_name);
        $this->assertEquals($role->title, $new_title);
    }
}
