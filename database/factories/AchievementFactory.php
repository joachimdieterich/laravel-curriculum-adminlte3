<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Achievement;
use Faker\Generator as Faker;

$factory->define(Achievement::class, function (Faker $faker) {
    return [
        'referenceable_type'    => 'App\EnablingObjective',
        'referenceable_id'      => 1,
        'status'                => '1',
    ];
});
