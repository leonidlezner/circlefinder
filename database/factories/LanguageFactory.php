<?php

use Faker\Generator as Faker;

$factory->define(\App\Language::class, function (Faker $faker) {
    $languageCode = $faker->languageCode . $faker->randomDigit;

    return [
        'title' => 'Lang_' . $languageCode,
        'code' => $languageCode
    ];
});
