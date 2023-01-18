<?php

namespace Database\Seeders;

use App\Group;
use Illuminate\Database\Seeder;

class GroupUserTableSeeder extends Seeder
{
    public function run()
    {
        Group::findOrFail(1)->users()->sync([1, 2, 3, 4, 5, 6, 7, 8]); //enrol all users
    }
}
