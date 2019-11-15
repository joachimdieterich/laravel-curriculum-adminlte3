<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Curriculum;
use Faker\Generator as Faker;

$factory->define(Curriculum::class, function (Faker $faker) {
    return [
        'title'                 => $faker->company,
        'description'           => $faker->paragraph,
        
        'author'                => $faker->userName,
        'publisher'             => $faker->company,
        'city'                  => $faker->city,
        'date'                  => $faker->dateTimeThisMonth()->format('Y-m-d H:i:s'),
        
        'color'                 => $faker->rgbColor,
        
        'grade_id'              => 1,
        'subject_id'            => 1,
        
        'state_id'              => 'DE-RP',
        'country_id'            => 'DE',
        'organization_type_id'  => 1,
        
        'medium_id'             => null, //define std. image
        
        'owner_id'              => 1,
       
    ];
});
