<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */
use App\EnablingObjective;
use Faker\Generator as Faker;

$factory->define(EnablingObjective::class, function (Faker $faker) {
    return [
        'title' => $faker->text,
        
        'description' => $faker->sentence,
        'time_approach' => null,
        'curriculum_id' => 1,
        'terminal_objective_id' => 1,
        'order_id' => 0,
        'level_id' => null,
        
    ];
});
