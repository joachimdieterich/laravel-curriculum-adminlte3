<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Logbook;
use Faker\Generator as Faker;

$factory->define(Logbook::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'description' => $faker->paragraph,

        'owner_id' => auth()->user()->id,

    ];
});
