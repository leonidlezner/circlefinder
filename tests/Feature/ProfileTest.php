<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\UsersAdmins;

/**
 * @group profile
 */
class ProfileTest extends TestCase
{
    use DatabaseMigrations;
    use UsersAdmins;

    public function testGuestCannotAcessProfile()
    {
        $response = $this->get(route('profile.index'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $user = $this->fetchUser();
        $response = $this->get(route('profile.show', ['uuid' => $user->uuid]));
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function testUserCanAccessOwnProfile()
    {
        $user = $this->fetchUser();

        $response = $this->actingAs($user)->get(route('profile.index'));
        $response->assertStatus(302);
        $response->assertRedirect(route('profile.show', [$user->uuid]));

        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get(route('profile.password.edit'));
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get(route('profile.avatar.edit'));
        $response->assertStatus(200);
    }

    public function testUserCanAccessSomeProfile()
    {
        $user = $this->fetchUser();
        $userB = $this->fetchUser();

        $response = $this->actingAs($user)->get(route('profile.show', ['uuid' => $userB->uuid]));

        $response->assertStatus(200);
    }

    public function testUserCanUpdateProfile()
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
                'timezone' => $faker->timezone,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertEquals($user->name, $new_name);
        $this->assertEquals($user->email, $new_mail);
        $this->assertEquals($user->about, $new_about);

        # Check if the same email is accepted by the validator
        $response = $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => $new_name,
                'email' => $user->email,
                'timezone' => $faker->timezone,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }

    public function testUserCanUpdatePassword()
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

    public function testUserCannotUpdateWithWrongPassword()
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

    public function testExtUserCannotChangePassword()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $user->no_password = true;
        $user->save();

        $new_pass = $faker->password;

        $response = $this->actingAs($user)
            ->put(route('profile.password.update'), [
                'current_password' => 'secret',
                'password' => $new_pass,
                'password_confirmation' => $new_pass,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    public function testUserCannotUploadNonImage()
    {
        $user = $this->fetchUser();

        Storage::fake('avatars');

        $response = $this->actingAs($user)->put(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->create('document.pdf', 256),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    public function testUserCanUploadNewAvatar()
    {
        $user = $this->fetchUser();

        Storage::fake('fakedisk');

        $min_upload_size = config('userprofile.avatar.min_upload_size');

        $response = $this->actingAs($user)->put(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('avatar.jpeg', $min_upload_size, $min_upload_size),
        ]);

        $this->assertFalse(is_null($user->avatar));

        Storage::disk('fakedisk')->assertExists('avatars/' . $user->avatar);

        $response = $this->actingAs($user)->get(route('profile.avatar.download', ['uuid' => $user->uuid]));

        $response->assertStatus(200);
    }

    public function testUserCanConvertNewAvatar()
    {
        $user = $this->fetchUser();

        Storage::persistentFake('fakedisk');

        $min_upload_size = config('userprofile.avatar.min_upload_size');

        $response = $this->actingAs($user)->put(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('avatar.jpeg', $min_upload_size, $min_upload_size),
        ]);

        Storage::disk('fakedisk')->assertExists('avatars/' . $user->avatar);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }

    public function testUserHasToUseImageAsAvatar()
    {
        $user = $this->fetchUser();

        Storage::persistentFake('fakedisk');

        $response = $this->actingAs($user)->put(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->create('document.jpg', 256),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }
}
