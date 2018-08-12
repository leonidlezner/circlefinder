<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\UsersAdmins;
use Carbon\Carbon;

/**
 * @group membership
 */
class MembershipTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    private function fetchMembership($data, $user)
    {
        $membership = new \App\Membership;

        $membership->fill($data);

        $membership->circle_id = 0;
        $membership->user_id = $user->id;

        $membership->save();

        return $membership;
    }

    public function testCreateNewMembership()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $data = [
            'type' => 'any',
            'begin' => $faker->date,
        ];

        $membership = $this->fetchMembership($data, $user);
        $membership = $this->fetchMembership($data, $user);
        $membership = $this->fetchMembership($data, $user);

        $this->assertDatabaseHas('memberships', [
            'user_id' => $user->id,
        ]);

        $this->assertEquals(3, count($user->memberships));

        $this->assertEquals($user->id, $membership->user->id);
        $this->assertEquals($data['type'], $membership->type);
        $this->assertEquals(new Carbon($data['begin']), $membership->begin);
    }

    public function testRemovingUserRemovesMemberships()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $data = [
            'type' => 'any',
            'begin' => $faker->date,
        ];

        $membership = $this->fetchMembership($data, $user);

        $this->assertDatabaseHas('memberships', [
            'user_id' => $user->id,
        ]);

        $user->delete();

        $this->assertDatabaseMissing('memberships', [
            'user_id' => $user->id,
        ]);
    }

    public function testAttachDetachLanguageToMembership()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $lang = factory(\App\Language::class)->create();
        $lang2 = factory(\App\Language::class)->create();

        $data = [
            'type' => 'any',
            'begin' => $faker->date,
        ];

        $membership = $this->fetchMembership($data, $user);

        $membership->languages()->attach($lang);
        $membership->languages()->attach($lang2);

        $this->assertDatabaseHas('language_membership', [
            'membership_id' => $membership->id,
        ]);

        $this->assertDatabaseHas('language_membership', [
            'language_id' => $lang->id,
        ]);

        $this->assertDatabaseHas('language_membership', [
            'language_id' => $lang2->id,
        ]);

        $membership->languages()->detach();

        $this->assertDatabaseMissing('language_membership', [
            'membership_id' => $membership->id,
        ]);
    }

    public function testValidationRules()
    {
        $rules = \App\Membership::validationRules();
        $rules2 = \App\Membership::validationRules(['type']);

        $this->assertTrue(count($rules) > 0);

        $this->assertTrue(key_exists('type', $rules));

        $this->assertFalse(key_exists('type', $rules2));
    }
}
