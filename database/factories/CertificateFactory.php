<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Certificate;
use Faker\Generator as Faker;

$factory->define(Certificate::class, function (Faker $faker) {
    return [
        'title' => $faker->company,
        'description' => $faker->paragraph,
        'body' => $faker->paragraph,
        'curriculum_id' => 1,
        'organization_id' => 1,
        'owner_id' => auth()->user()->id,

    ];
});
