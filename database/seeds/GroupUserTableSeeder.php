<?php

use App\Group;
use Illuminate\Database\Seeder;

class GroupUserTableSeeder extends Seeder
{
    public function run()
    {
        Group::findOrFail(1)->users()->sync(1);
    }
}
