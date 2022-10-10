<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\OrganizationType;
use Faker\Generator as Faker;

$factory->define(OrganizationType::class, function (Faker $faker) {
    return [
        'title'       => $faker->company,
        'external_id' => $faker->numberBetween(1, 100),
        'state_id'    => 'DE-RP',
        'country_id'  => 'DE',
    ];
});
