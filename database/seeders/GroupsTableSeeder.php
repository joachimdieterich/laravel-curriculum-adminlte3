<?php
namespace Database\Seeders;
use App\Group;
use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{
    public function run()
    {
        $groups = [[
            'id'=>1,
            'title'=>'Testlerngruppe',
            'grade_id'=>5,
            'period_id'=>1,
            'organization_id'=>1,
        ],

        ];

        Group::insert($groups);
    }
}
