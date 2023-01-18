<?php

namespace Database\Seeders;

use App\Curriculum;
use Illuminate\Database\Seeder;

class CurriculumTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $curricula = [[
            'id' => 1,
            'title' => 'Test Curriculum',
            'description' => 'Curriculum Description',
            'author' => 'JoeDiet',
            'publisher' => 'curriculumonline',
            'city' => 'Ilbesheim',
            'grade_id' => 1,
            'subject_id' => 1,
            'organization_type_id' => 1,
            'state_id' => 'DE-RP',
            'country_id' => 'DE',
            'owner_id' => 1,
        ],
        ];

        Curriculum::insert($curricula);
    }
}
