<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Traits\UsersAdmins;
use Illuminate\Support\Facades\Artisan;

/**
 * @group events
 */
class EventsTest extends TestCase
{
    use DatabaseMigrations;
    use UsersAdmins;

    public function testUserJoinedCircleEvent()
    {
        $user = $this->fetchUser();
        $user2 = $this->fetchUser();
        $faker = $this->fetchFaker();

        $circle = $this->fetchCircle($user);

        $circle->joinWithDefaults($user2);
    }
}
