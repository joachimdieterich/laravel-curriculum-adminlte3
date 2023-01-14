<?php

namespace Database\Seeders;

use App\ObjectiveType;
use Illuminate\Database\Seeder;

class ObjectiveTypeTableSeeder extends Seeder
{
    public function run()
    {
        $objectiveTypes = [
            [
                'id' => 1,
                'title' => 'Kompetenzen',
                'created_at' => '2019-06-20 11:29:32',
                'updated_at' => '2019-06-20 11:29:32',
            ],
            [
                'id' => 2,
                'title' => 'Thema/Inhalt',
                'created_at' => '2019-06-20 11:29:32',
                'updated_at' => '2019-06-20 11:29:32',
            ],
            [
                'id' => 3,
                'title' => 'Methode',
                'created_at' => '2019-06-20 11:29:32',
                'updated_at' => '2019-06-20 11:29:32',
            ],
            [
                'id' => 4,
                'title' => 'Bildungsstandards',
                'created_at' => '2019-06-20 11:29:32',
                'updated_at' => '2019-06-20 11:29:32',
            ],
            [
                'id' => 5,
                'title' => 'Themenfelder (Einführungsphase)',
                'created_at' => '2019-06-20 11:29:32',
                'updated_at' => '2019-06-20 11:29:32',
            ],
            [
                'id' => 6,
                'title' => 'Themenfelder (Qualifikationsphase)',
                'created_at' => '2019-06-20 11:29:32',
                'updated_at' => '2019-06-20 11:29:32',
            ],
        ];

        ObjectiveType::insert($objectiveTypes);
    }
}
