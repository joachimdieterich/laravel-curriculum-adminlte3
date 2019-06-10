<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Role;
use App\Permission;
use Faker\Generator as Faker;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'title'       => $faker->jobTitle,
        'permissions' => Permission::all()->pluck('id')->toArray()
    ];
});
