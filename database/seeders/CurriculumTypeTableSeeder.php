<?php
namespace Database\Seeders;
use App\CurriculumType;
use Illuminate\Database\Seeder;

class CurriculumTypeTableSeeder extends Seeder
{
    public function run()
    {
        $curriculumTypes = [
            [
                'id'             => 1,
                'title'       => 'global',
                'created_at'     => '2019-06-20 11:29:32',
                'updated_at'     => '2019-06-20 11:29:32',
            ],
            [
                'id'             => 2,
                'title'       => 'organization',
                'created_at'     => '2019-06-20 11:29:32',
                'updated_at'     => '2019-06-20 11:29:32',
            ],
            [
                'id'             => 3,
                'title'       => 'group',
                'created_at'     => '2019-06-20 11:29:32',
                'updated_at'     => '2019-06-20 11:29:32',
            ],
            [
                'id'             => 4,
                'title'       => 'user',
                'created_at'     => '2019-06-20 11:29:32',
                'updated_at'     => '2019-06-20 11:29:32',
            ],

        ];

        CurriculumType::insert($curriculumTypes);
    }
}
