<?php

namespace Database\Seeders;

use App\Permission;
use App\Role;
use Illuminate\Database\Seeder;

class ExtendPermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        $all_permissions = Permission::all();
        Role::where('title', 'Administrator')->first()->permissions()->sync($all_permissions->pluck('id'));

        $schoolAdmin_permissions = $all_permissions->filter(function ($permission) {
            $schoolAdmin_permissions_titles = [
                'assignement_access',
                'assignment_create',
                'assignment_show',
                'assignment_delete',
                'test_access',
                'test_show',
                'tool_access',
                'tool_show',
                'user_access',
                'user_create',
                'user_edit',
                'user_show',
                'user_delete',
            ];

            return in_array($permission->title, $schoolAdmin_permissions_titles);
        });

        Role::where('title', 'Schooladmin')->first()->permissions()->syncWithoutDetaching($schoolAdmin_permissions->pluck('id'));

        $teacher_permissions = $schoolAdmin_permissions->filter(function ($permission) {
            $teacher_permissions_titles = [
                'assignement_access',
                'assignment_create',
                'assignment_show',
                'assignment_delete',
                'test_access',
                'test_show',
                'user_show',
            ];

            return in_array($permission->title, $teacher_permissions_titles);
        });

        Role::where('title', 'Teacher')->first()->permissions()->syncWithoutDetaching($teacher_permissions->pluck('id'));

        $student_permissions = $teacher_permissions->filter(function ($permission) {
            $teacher_permissions_titles = [
                'assignment_show',
                'test_show',
            ];

            return in_array($permission->title, $teacher_permissions_titles);
        });

        Role::where('title', 'Student')->first()->permissions()->syncWithoutDetaching($student_permissions->pluck('id'));
    }
}
