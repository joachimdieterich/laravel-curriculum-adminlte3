<?php

namespace Database\Seeders;

use App\Permission;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExtendPermissionTableSeeder extends Seeder
{
    public function run(): void
    {
        $lastId = Permission::orderBy('id', 'desc')->first()->id;
        $now    = Carbon::now();

        $existingPermissions = Permission::all()->pluck('title')->toArray();

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
            'tag_access',
            'tag_create',
            'tag_edit',
            'tag_show',
            'tag_delete',
        ];

        $permissions = [];
        foreach (collect($titles)->diff($existingPermissions)->toArray() as $title) {
            $lastId += 1;

            $permissions[] = [
                'id'         => $lastId,
                'title'      => $title,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        Permission::insert($permissions);
    }
}
