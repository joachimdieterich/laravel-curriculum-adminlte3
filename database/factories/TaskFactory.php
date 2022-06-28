<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    $start_date = $faker->dateTimeThisYear();
    $start_date_clone = clone $start_date;
    $end_date = $faker->dateTimeBetween($start_date, $start_date_clone->modify('+1 year'));

    $start_date_string = $start_date->format('Y-m-d H:i:s');
    $end_date_string = $end_date->format('Y-m-d H:i:s');

    return [
        'title'       => $faker->company,
        'description' => $faker->sentence,
        'start_date'  => $start_date_string,
        'due_date'    => $end_date_string,
        'owner'       => auth()->user()->id,
    ];
});
