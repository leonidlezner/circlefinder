<?php

use Faker\Generator as Faker;

$factory->define(\App\Circle::class, function (Faker $faker) {
    return [
        'user_id' => \App\User::inRandomOrder()->get()->first(),
        'type' => $faker->randomElement(config('circle.defaults.types')),
        'title' =>  $faker->catchPhrase,
        'completed' => false,
        'full' => false,
        'limit' => 5,
        'description' => $faker->text,
        'begin' => today()
    ];
});
