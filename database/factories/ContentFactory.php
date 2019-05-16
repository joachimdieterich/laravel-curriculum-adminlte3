<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Content;
use Faker\Generator as Faker;

$factory->define(Content::class, function (Faker $faker) {
    return [
        'title'       => $faker->title,
        'content'     => $faker->paragraph,
        'owner_id'    => factory(App\User::class),
        //
    ];
});
