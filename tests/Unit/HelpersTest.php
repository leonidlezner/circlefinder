<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\UsersAdmins;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

/**
 * @group helpers
 */
class HelpersTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    private function setAvatar($user)
    {
        Storage::fake('fakedisk');

        $min_upload_size = config('userprofile.avatar.min_upload_size');

        $response = $this->actingAs($user)->put(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('avatar.jpeg', $min_upload_size, $min_upload_size),
        ]);
    }

    public function testListLanguages()
    {
        $languages = $this->fetchLanguage(null, 5);

        $languages_string = list_languages($languages);

        $this->assertEquals($languages->sortBy('code')->implode('title', ', '), $languages_string);

        $languages_string = list_languages($languages, 2);

        $this->assertEquals(
            $languages->sortBy('code')->slice(0, 2)->implode('title', ', ') . ', ...',
            $languages_string
        );
    }

    public function testCircleState()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();

        $faker = $this->fetchFaker();
        $circle = $this->fetchCircle($user);

        $this->assertEquals('Open', circle_state($circle));

        $circle->limit = 2;
        $circle->save();
        $circle->joinWithDefaults($user);
        $circle->joinWithDefaults($user2);
        $circle->uncomplete();

        $this->assertEquals('Full', circle_state($circle));
        
        $circle->complete();

        $this->assertEquals('Completed', circle_state($circle));
    }

    public function testGoodTitle()
    {
        $user = $this->fetchUser();
        $this->assertEquals('User "' . $user->name . '"', good_title($user));

        $circle = $this->fetchCircle($user);
        $this->assertEquals($circle->title . ' (' . (string) $circle . ')', good_title($circle));
    }

    public function testUserAvatar()
    {
        $user = $this->fetchUser();

        $this->setAvatar($user);

        $avatar = user_avatar($user, 300);

        $url = user_avatar($user, 300, true);

        $this->assertEquals(
            route('profile.avatar.download.resized', ['uuid' => $user->uuid, 'w' => 300, 'h' => 300]),
            $url
        );
    }
}
