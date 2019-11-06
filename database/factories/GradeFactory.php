<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Grade;
use Faker\Generator as Faker;

$factory->define(Grade::class, function (Faker $faker) {
    $begin = $faker->numberBetween(1, 13);
    $end = $faker->numberBetween($begin, 13);
    return [
        'title'                 => "Grade {$begin}/{$end}",
        'external_begin'        => $begin,
        'external_end'          => $end,
        'organization_type_id'  => $faker->numberBetween(1, 10) //get random organization_type_id of seeded types
    ];
});

