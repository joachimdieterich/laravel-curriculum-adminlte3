<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Permission;
use Faker\Generator as Faker;

$factory->define(Permission::class, function (Faker $faker) {
    return [
        'title'       => $faker->jobTitle,
    ];
});
