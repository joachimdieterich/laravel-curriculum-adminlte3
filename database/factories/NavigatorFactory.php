<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Navigator;
use Faker\Generator as Faker;

$factory->define(Navigator::class, function (Faker $faker) {
    return [
        'title'             => $faker->title,
        'organization_id'   => 1
    ];
});

