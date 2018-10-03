<?php

namespace Tests\Traits;

use Faker\Factory as Faker;
use Illuminate\Support\Facades\Artisan;

trait UsersAdmins
{
    private function fetchAdmin()
    {
        return factory(\App\Admin::class)->create();
    }

    private function fetchUser($id = null)
    {
        if ($id == null) {
            return factory(\App\User::class)->create();
        } else {
            return \App\User::find($id);
        }
    }

    private function fetchFaker()
    {
        return Faker::create();
    }

    private function fetchRole($id = null)
    {
        if ($id == null) {
            return factory(\App\Role::class)->create();
        } else {
            return \App\Role::find($id);
        }
    }

    public function fetchModerator()
    {
        $user = $this->fetchUser();

        $role = \App\Role::firstOrCreate([
            'name' => 'moderator',
            'title' => 'Moderator',
        ]);

        $user->roles()->attach($role);

        return $user;
    }

    private function fetchLanguage($id = null, $count = 1)
    {
        if ($id === null) {
            if ($count > 1) {
                return factory(\App\Language::class, $count)->create();
            } else {
                return factory(\App\Language::class)->create();
            }
        } else {
            return \App\Language::find($id);
        }
    }

    private function fetchCircle($owner, $data = null)
    {
        $faker = $this->fetchFaker();

        if (is_null($data)) {
            $data = [
                'type' => $faker->randomElement(config('circle.defaults.types')),
                'title' => $faker->catchPhrase,
                'limit' => config('circle.defaults.limit'),
                'begin' => today(),
            ];
        }

        return $owner->circles()->create($data);
    }

    private function fetchMessage($circle, $user = null, $recipients = array())
    {
        $faker = $this->fetchFaker();

        $message = new \App\Message;

        $message->body = $faker->text;

        $message->recipients = array_map(function ($r) {
            return $r->id;
        }, $recipients);

        $message->user_id = $user ? $user->id : null;
        $message->circle_id = $circle->id;
        $message->show_to_all = false;
        
        $message->save();

        return $message;
    }

    private function seedLanguages()
    {
        Artisan::call('db:seed', ['--class' => 'LanguagesTableSeeder', '--env' => 'testing']);
    }
}
