<?php
namespace Database\Seeders;

use App\Permission;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExtendPermissionTableSeeder extends Seeder
{
    public function run()
    {
        $id = Permission::orderBy('id', 'desc')->first()->id;
        $now = Carbon::now();
        $titles = [
            'assignment_access',
            'assignment_create',
            'assignment_edit',
            'assignment_show',
            'assignment_delete',
            'test_access',
            'test_create',
            'test_edit',
            'test_show',
            'test_delete',
            'tool_access',
            'tool_create',
            'tool_edit',
            'tool_show',
            'tool_delete',

        ];
        $permissions = [];

        foreach ($titles as $title) {
            $id += 1;
            $permission = [
              'id' => $id,
              'title' => $title,
              'created_at' => $now,
              'updated_at' => $now

            ];

            array_push($permissions, $permission);
        }
        Permission::insert($permissions);
    }
}
