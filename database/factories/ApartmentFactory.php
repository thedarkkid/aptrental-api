<?php

use Faker\Generator as Faker;

$factory->define(\App\Apartment::class, function (Faker $faker) {
    return [
        'name' => $faker->text(15),
        'description' => $faker->text(200),
        'floor_area_size' => $faker->numberBetween(100, 530),
        'price_per_month' => $faker->numberBetween(200, 1000),
        'number_of_rooms' => $faker->numberBetween(1, 5),
        'longitude' => $faker->longitude,
        'latitude' => $faker->latitude,
        'address' => $faker->address,
        'user_id' => 2,
        'status' => $faker->numberBetween(0, 1),
    ];
});

