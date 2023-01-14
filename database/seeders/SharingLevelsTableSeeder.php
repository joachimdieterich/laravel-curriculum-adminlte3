<?php

namespace Database\Seeders;

use App\SharingLevel;
use Illuminate\Database\Seeder;

class SharingLevelsTableSeeder extends Seeder
{
    public function run()
    {
        $sharinglevels = [
            [
                'id' => 1,
                'lang_en' => 'global',
                'lang_de' => 'global',
            ],
            [
                'id' => 2,
                'lang_en' => 'organization',
                'lang_de' => 'Organization',
            ],
            [
                'id' => 3,
                'lang_en' => 'group',
                'lang_de' => 'Lerngruppe',
            ],
            [
                'id' => 4,
                'lang_en' => 'user',
                'lang_de' => 'Benutzer',
            ],

        ];

        SharingLevel::insert($sharinglevels);
    }
}
