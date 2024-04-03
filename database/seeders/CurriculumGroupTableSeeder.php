<?php

namespace Database\Seeders;

use App\Curriculum;
use App\CurriculumSubscription;
use Illuminate\Database\Seeder;

class CurriculumGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Curriculum::findOrFail(1)->groups()->sync(1);
        $subscribe = CurriculumSubscription::updateOrCreate([
            'curriculum_id' => 1,
            'subscribable_type' => "App\Group",
            'subscribable_id' => 1,
        ], [
            'editable' => false,
            'owner_id' => 1,
        ]);
        $subscribe->save();
    }
}
