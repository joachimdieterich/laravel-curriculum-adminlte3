<?php

namespace Database\Seeders;

use App\Grade;
use Illuminate\Database\Seeder;

class GradesTableSeeder extends Seeder
{
    public function run()
    {
        $grades = [[
            'id' => 1,
            'title' => '1. Klasse',
            'external_begin' => 1,
            'external_end' => 1,
            'organization_type_id' => 5,
        ],
            [
                'id' => 2,
                'title' => '2. Klasse',
                'external_begin' => 2,
                'external_end' => 2,
                'organization_type_id' => 5,
            ],
            [
                'id' => 3,
                'title' => '3. Klasse',
                'external_begin' => 3,
                'external_end' => 3,
                'organization_type_id' => 5,
            ],
            [
                'id' => 4,
                'title' => '4. Klasse',
                'external_begin' => 4,
                'external_end' => 4,
                'organization_type_id' => 5,
            ],
            [
                'id' => 5,
                'title' => '5. Klasse',
                'external_begin' => 5,
                'external_end' => 5,
                'organization_type_id' => 22,
            ],
            [
                'id' => 6,
                'title' => '6. Klasse',
                'external_begin' => 6,
                'external_end' => 6,
                'organization_type_id' => 22,
            ],
            [
                'id' => 7,
                'title' => '7. Klasse',
                'external_begin' => 7,
                'external_end' => 7,
                'organization_type_id' => 22,
            ],
            [
                'id' => 8,
                'title' => '8. Klasse',
                'external_begin' => 8,
                'external_end' => 8,
                'organization_type_id' => 22,
            ],
            [
                'id' => 9,
                'title' => '9. Klasse',
                'external_begin' => 9,
                'external_end' => 9,
                'organization_type_id' => 22,
            ],
            [
                'id' => 10,
                'title' => '10. Klasse',
                'external_begin' => 10,
                'external_end' => 10,
                'organization_type_id' => 22,
            ],
            [
                'id' => 11,
                'title' => '11. Klasse',
                'external_begin' => 11,
                'external_end' => 11,
                'organization_type_id' => 26,
            ],
            [
                'id' => 12,
                'title' => '12. Klasse',
                'external_begin' => 12,
                'external_end' => 12,
                'organization_type_id' => 26,
            ],
            [
                'id' => 13,
                'title' => '13. Klasse',
                'external_begin' => 13,
                'external_end' => 13,
                'organization_type_id' => 26,
            ],
            [
                'id' => 48,
                'title' => 'Sekundarstufe I',
                'external_begin' => 5,
                'external_end' => 13,
                'organization_type_id' => 22,
            ],
            [
                'id' => 53,
                'title' => 'Universität',
                'external_begin' => 99,
                'external_end' => 99,
                'organization_type_id' => 1,
            ],
            [
                'id' => 145,
                'title' => 'Erwachsenenbildung',
                'external_begin' => 99,
                'external_end' => 99,
                'organization_type_id' => 1,
            ],
            [
                'id' => 146,
                'title' => '1.-4. Klasse',
                'external_begin' => 1,
                'external_end' => 4,
                'organization_type_id' => 5,
            ],
            [
                'id' => 150,
                'title' => 'Sekundarstufe II',
                'external_begin' => 11,
                'external_end' => 13,
                'organization_type_id' => 26,
            ],
            [
                'id' => 151,
                'title' => '7.-10. Klasse',
                'external_begin' => 7,
                'external_end' => 10,
                'organization_type_id' => 22,
            ],
            [
                'id' => 152,
                'title' => '5.-6. Klasse',
                'external_begin' => 5,
                'external_end' => 6,
                'organization_type_id' => 22,
            ],
            [
                'id' => 153,
                'title' => '7.-9. Klasse',
                'external_begin' => 7,
                'external_end' => 9,
                'organization_type_id' => 22,
            ],
            [
                'id' => 154,
                'title' => '7.-8. Klasse',
                'external_begin' => 7,
                'external_end' => 8,
                'organization_type_id' => 22,
            ],
            [
                'id' => 155,
                'title' => '9.-10. Klasse',
                'external_begin' => 9,
                'external_end' => 10,
                'organization_type_id' => 22,
            ],
            [
                'id' => 156,
                'title' => '1.-10. Klasse',
                'external_begin' => 1,
                'external_end' => 10,
                'organization_type_id' => 1,
            ],
            [
                'id' => 157,
                'title' => '6.-10. Klasse',
                'external_begin' => 6,
                'external_end' => 10,
                'organization_type_id' => 22,
            ],
            [
                'id' => 158,
                'title' => '8.-10. Klasse',
                'external_begin' => 8,
                'external_end' => 10,
                'organization_type_id' => 22,
            ],
            [
                'id' => 159,
                'title' => '5.-10. Klasse',
                'external_begin' => 5,
                'external_end' => 10,
                'organization_type_id' => 22,
            ],
            [
                'id' => 160,
                'title' => '1.-2. Klasse',
                'external_begin' => 1,
                'external_end' => 2,
                'organization_type_id' => 5,
            ],
            [
                'id' => 161,
                'title' => '3.-4. Klasse',
                'external_begin' => 3,
                'external_end' => 4,
                'organization_type_id' => 5,
            ],
            [
                'id' => 162,
                'title' => 'Divers/ohne',
                'external_begin' => 99,
                'external_end' => 99,
                'organization_type_id' => 1,
            ],
            [
                'id' => 163,
                'title' => 'Allgb.Schule',
                'external_begin' => 70,
                'external_end' => 70,
                'organization_type_id' => 1,
            ],
            [
                'id' => 164,
                'title' => 'BBS',
                'external_begin' => 80,
                'external_end' => 80,
                'organization_type_id' => 1,
            ],
            [
                'id' => 999,
                'title' => 'Default',
                'external_begin' => 999,
                'external_end' => 999,
                'organization_type_id' => 1,
            ],
        ];

        Grade::insert($grades);
    }
}
