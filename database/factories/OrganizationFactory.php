<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Organization;
use Faker\Generator as Faker;

$factory->define(Organization::class, function (Faker $faker) {
    return [
        'title'       => $faker->company,
        'description' => $faker->sentence,

        'street'      => $faker->streetAddress,
        'postcode'    => $faker->postcode,
        'city'        => $faker->city,
        'phone'       => $faker->phoneNumber,
        'email'       => $faker->email,

        'status'      => 1,
    ];
});

