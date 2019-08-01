<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Medium;
use Faker\Generator as Faker;

$factory->define(Medium::class, function (Faker $faker) {
    return [
        
        'path'          => $faker->file('/tmp', '/tmp/'),
        'medium_name'    => $faker->word.$faker->fileExtension,
        'title'         => $faker->word,
        'description'   => $faker->sentence,
        
        'author'        => $faker->name,
        'publisher'     => $faker->company,
        'city'          => $faker->city,
        'date'          => $faker->dateTime,
        
        'size'          => $faker->numberBetween(1, 3000000).'kb',
        'mime_type'     => $faker->mimeType,

        'license_id'    => 1,
        'owner_id'      => 1,
    ];
});
