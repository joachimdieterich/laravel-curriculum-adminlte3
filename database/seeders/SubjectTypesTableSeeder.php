<?php

namespace Database\Seeders;

use App\SubjectType;
use Illuminate\Database\Seeder;

class SubjectTypesTableSeeder extends Seeder
{
    public function run()
    {
        $subject_types = [[
            'id' => 1,
            'title' => 'Keine Spezifizierung',
            'external_id' => 0,
        ],
            [
                'id' => 2,
                'title' => 'Pflichtfach, Grundfach und Leistungsfach',
                'external_id' => 1,
            ],
            [
                'id' => 3,
                'title' => 'Grundfach und Leistungsfach',
                'external_id' => 2,
            ],
            [
                'id' => 4,
                'title' => 'Pflichtfach',
                'external_id' => 3,
            ],
            [
                'id' => 5,
                'title' => 'Grundfach / Grundstufe',
                'external_id' => 4,
            ],
            [
                'id' => 6,
                'title' => 'Leistungsfach',
                'external_id' => 5,
            ],
            [
                'id' => 7,
                'title' => 'Wahlfach und Wahlpflichtfach',
                'external_id' => 6,
            ],
            [
                'id' => 8,
                'title' => 'Wahlfach',
                'external_id' => 7,
            ],
            [
                'id' => 9,
                'title' => 'Wahlpflichtfach',
                'external_id' => 8,
            ],
            [
                'id' => 10,
                'title' => 'Grundkurs und Aufbaukurs',
                'external_id' => 9,
            ],
            [
                'id' => 11,
                'title' => 'Grundkurs',
                'external_id' => 10,
            ],
            [
                'id' => 12,
                'title' => 'Aufbaukurs',
                'external_id' => 11,
            ],

        ];

        SubjectType::insert($subject_types);
    }
}
