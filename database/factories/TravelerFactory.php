<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Traveler::class, function (Faker $faker) {
    return [
        'booking_id' => $faker->unique()->numberBetween(1, 10000),
        'traveler_name' => $faker->name(),
        'traveler_email' => $faker->email,
        'departure_date' => $faker->dateTimeBetween('-7 days', '+21 days'),
        'return_date' => $faker->dateTimeBetween('-7 days', '+49 days'),
        'destination_country' => $faker->country,
        'destination_city' => $faker->city
    ];
});
