<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */
use App\TerminalObjective;
use Faker\Generator as Faker;

$factory->define(TerminalObjective::class, function (Faker $faker) {
    return [
        'title' => $faker->text,
        
        'description' => $faker->sentence,
        'curriculum_id' => 1,
        'objective_type_id' => 1,
    ];
});
