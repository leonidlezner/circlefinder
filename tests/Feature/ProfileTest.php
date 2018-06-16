<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Traits\UsersAdmins;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @group profile
 */
class ProfileTest extends TestCase
{
    use DatabaseMigrations;
    use UsersAdmins;

    public function test_guest_cannot_acess_profile()
    {
        $response = $this->get(route('profile.index'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $user = $this->fetchUser();
        $response = $this->get(route('profile.show', ['uuid' => $user->uuid]));
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_user_can_access_own_profile()
    {
        $user = $this->fetchUser();

        $response = $this->actingAs($user)->get(route('profile.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get(route('profile.password.edit'));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get(route('profile.avatar.edit'));
        $response->assertStatus(200);
    }

    public function test_user_can_access_some_profile()
    {
        $user = $this->fetchUser();
        $userB = $this->fetchUser();

        $response = $this->actingAs($user)->get(route('profile.show', ['uuid' => $userB->uuid]));
        $response->assertStatus(200);
    }

    public function test_user_can_update_profile()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $new_mail = $faker->unique()->safeEmail;
        $new_name = $faker->name;
        $new_about = $faker->text;

        $response = $this->actingAs($user)
                            ->put(route('profile.update'), [
                                'name' => $new_name,
                                'email' => $new_mail,
                                'about' => $new_about,
                            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertEquals($user->name, $new_name);
        $this->assertEquals($user->email, $new_mail);
        $this->assertEquals($user->about, $new_about);
        
        # Check if the same email is accepter by the validator
        $response = $this->actingAs($user)
                ->put(route('profile.update'), [
                    'name' => $new_name,
                    'email' => $user->email
                ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }

    public function test_user_can_update_password()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $new_pass = $faker->password;

        $response = $this->actingAs($user)
                            ->put(route('profile.password.update'), [
                                'current_password' => 'secret',
                                'password' => $new_pass,
                                'password_confirmation' => $new_pass,
                            ]);
        
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check($new_pass, $user->password));
    }

    public function test_user_cannot_update_with_wrong_password()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $new_pass = $faker->password;

        $response = $this->actingAs($user)
                            ->put(route('profile.password.update'), [
                                'current_password' => 'blabla',
                                'password' => $new_pass,
                                'password_confirmation' => $new_pass,
                            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    public function test_user_cannot_upload_non_image()
    {
        $user = $this->fetchUser();

        Storage::fake('avatars');

        $response = $this->actingAs($user)->put(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->create('document.pdf', 256)
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    public function test_user_can_upload_new_avatar()
    {
        $user = $this->fetchUser();

        Storage::fake('fakedisk');

        $response = $this->actingAs($user)->put(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('avatar.jpeg')
        ]);

        $this->assertFalse(is_null($user->avatar));

        Storage::disk('fakedisk')->assertExists('avatars/' . $user->avatar);

        $response = $this->actingAs($user)->get(route('profile.avatar.download', ['file' => $user->avatar]));

        $response->assertStatus(200);
    }

    public function test_user_can_convert_new_avatar()
    {
        $user = $this->fetchUser();

        Storage::persistentFake('fakedisk');

        $response = $this->actingAs($user)->put(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('avatar.png')
        ]);

        Storage::disk('fakedisk')->assertExists('avatars/' . $user->avatar);
    }
}
