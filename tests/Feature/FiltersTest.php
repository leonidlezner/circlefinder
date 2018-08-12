<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\UsersAdmins;
use Illuminate\Support\Facades\Artisan;

/**
 * @group filters
 */
class FiltersTest extends TestCase
{
    use RefreshDatabase;
    use UsersAdmins;

    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed', ['--class' => 'LanguagesTableSeeder', '--env' => 'testing']);
    }

    public function testUserCanFilterByType()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        
        $circle1 = $this->fetchCircle($user, [
            'type' => 'f2f',
            'title' => $faker->catchPhrase,
            'limit' => 5,
            'begin' => today(),
        ]);

        $circle2 = $this->fetchCircle($user, [
            'type' => 'virtual',
            'title' => $faker->catchPhrase,
            'limit' => 5,
            'begin' => today(),
        ]);

        $circle3 = $this->fetchCircle($user, [
            'type' => 'f2f',
            'title' => $faker->catchPhrase,
            'limit' => 5,
            'begin' => today(),
        ]);

        $response = $this->actingAs($user)->get(route('circles.index', ['type' => 'f2f']));

        $response->assertStatus(200);

        $list = [
            sprintf('data-uuid="%s"', $circle3->uuid),
            sprintf('data-uuid="%s"', $circle1->uuid),
        ];

        $response->assertSeeInOrder($list);
    }

    public function testUserCanFilterByStatusOpen()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        
        $circle1 = $this->fetchCircle($user, [
            'type' => 'f2f',
            'title' => $faker->catchPhrase,
            'limit' => 5,
            'begin' => today(),
        ]);

        $circle2 = $this->fetchCircle($user, [
            'type' => 'virtual',
            'title' => $faker->catchPhrase,
            'limit' => 5,
            'begin' => today(),
        ]);

        $circle2->complete();

        $response = $this->actingAs($user)->get(route('circles.index', ['status' => 'open']));

        $response->assertStatus(200);

        $list = [
            sprintf('data-uuid="%s"', $circle1->uuid),
        ];

        $response->assertSeeInOrder($list);
    }

    public function testUserCanFilterByStatusCompleted()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        
        $circle1 = $this->fetchCircle($user, [
            'type' => 'f2f',
            'title' => $faker->catchPhrase,
            'limit' => 5,
            'begin' => today(),
        ]);

        $circle2 = $this->fetchCircle($user, [
            'type' => 'virtual',
            'title' => $faker->catchPhrase,
            'limit' => 5,
            'begin' => today(),
        ]);

        $circle2->complete();

        $response = $this->actingAs($user)->get(route('circles.index', ['status' => 'completed']));

        $response->assertStatus(200);

        $list = [
            sprintf('data-uuid="%s"', $circle2->uuid),
        ];

        $response->assertSeeInOrder($list);
    }

    public function testUserCanFilterByStatusFull()
    {
        $user = $this->fetchUser();
        $faker = $this->fetchFaker();
        
        $circle1 = $this->fetchCircle($user, [
            'type' => 'f2f',
            'title' => $faker->catchPhrase,
            'limit' => 5,
            'begin' => today(),
        ]);

        $circle2 = $this->fetchCircle($user, [
            'type' => 'virtual',
            'title' => $faker->catchPhrase,
            'limit' => 3,
            'begin' => today(),
        ]);

        $circle2->joinWithDefaults($this->fetchUser());
        $circle2->joinWithDefaults($this->fetchUser());
        $circle2->joinWithDefaults($this->fetchUser());

        $response = $this->actingAs($user)->get(route('circles.index', ['status' => 'full']));

        $response->assertStatus(200);

        $list = [
            sprintf('data-uuid="%s"', $circle2->uuid),
        ];

        $response->assertSeeInOrder($list);
    }

    public function testUserCanFilterByLanguage()
    {
        $this->seedLanguages();

        $user = $this->fetchUser();
        $faker = $this->fetchFaker();

        $lang1 = \App\Language::all()->get(0);
        $lang2 = \App\Language::all()->get(1);
        
        $circle1 = $this->fetchCircle($user, [
            'type' => 'f2f',
            'title' => $faker->catchPhrase,
            'limit' => 5,
            'begin' => today(),
        ]);

        $circle2 = $this->fetchCircle($user, [
            'type' => 'virtual',
            'title' => $faker->catchPhrase,
            'limit' => 3,
            'begin' => today()
        ]);

        $circle1->languages()->attach($lang1);
        $circle2->languages()->attach($lang2);

        $response = $this->actingAs($user)->get(route('circles.index', ['language' => $lang2->code]));

        $response->assertStatus(200);

        $list = [
            sprintf('data-uuid="%s"', $circle2->uuid),
        ];

        $response->assertSeeInOrder($list);
    }
}
