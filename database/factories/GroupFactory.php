<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Group;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker) {
    return [
        'title'             => $faker->company,
        
        'grade_id'          => 5,
        'period_id'         => 1,
        'organization_id'   => 1,
      
    ];
});

