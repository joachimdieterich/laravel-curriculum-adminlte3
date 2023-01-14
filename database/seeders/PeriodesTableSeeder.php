<?php

namespace Database\Seeders;

use App\Period;
use Illuminate\Database\Seeder;

class PeriodesTableSeeder extends Seeder
{
    public function run()
    {
        $periodes = [[
            'id' => 1,
            'title' => 'Test',
            'begin' => '2019-04-15 19:14:42',
            'end' => '2023-04-15 19:14:42',
            //  'organization_id' => 1,
            'owner_id' => 1,
        ],

        ];

        Period::insert($periodes);
    }
}
