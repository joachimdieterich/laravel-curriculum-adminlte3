<?php
namespace Database\Seeders;
use App\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [[
            'id'         => 1,
            'title'      => 'Administrator',
            'created_at' => '2019-04-15 19:13:32',
            'updated_at' => '2019-04-15 19:13:32',
            'deleted_at' => null,
        ],
            [
                'id'         => 2,
                'title'      => 'Creator',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null,
            ],
            [
                'id'         => 3,
                'title'      => 'Indexer',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null,
            ],
            [
                'id'         => 4,
                'title'      => 'Schooladmin',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null,
            ],
            [
                'id'         => 5,
                'title'      => 'Teacher',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null,
            ],
            [
                'id'         => 6,
                'title'      => 'Student',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null,
            ],
            [
                'id'         => 7,
                'title'      => 'Parent',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null,
            ],
            [
                'id'         => 8,
                'title'      => 'Guest',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null,
            ],
            [
                'id'         => 9,
                'title'      => 'Token',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'deleted_at' => null,
            ],
        ];

        Role::insert($roles);
    }
}
