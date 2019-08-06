<?php

use App\User;
use App\OrganizationRoleUser;
use Illuminate\Database\Seeder;
use App\Notifications\Welcome;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
            'id'             => 1,
            'common_name'    => 'cn_Admin',
            'username'       => 'Admin',
            'firstname'      => 'Global',
            'lastname'       => 'Admin',
            'email'          => 'admin@curriculumonline.de',
            'password'       => '$2y$10$imU.Hdz7VauIT3LIMCMbsOXvaaTQg6luVqkhfkBcsUd.SJW2XSRKO',
            'remember_token' => null,
            'created_at'     => '2019-04-15 19:13:32',
            'updated_at'     => '2019-04-15 19:13:32',
            'deleted_at'     => null,
            'current_organization_id'     => 1,
            ],
            [
            'id'             => 2,
            'common_name'    => 'cn_Student',
            'username'       => 'Student',
            'firstname'      => 'Student',
            'lastname'       => 'Role',
            'email'          => 'student@curriculumonline.de',
            'password'       => '$2y$10$imU.Hdz7VauIT3LIMCMbsOXvaaTQg6luVqkhfkBcsUd.SJW2XSRKO',
            'remember_token' => null,
            'created_at'     => '2019-04-15 19:13:32',
            'updated_at'     => '2019-04-15 19:13:32',
            'deleted_at'     => null,
            'current_organization_id'     => 1,
            ]
            
        ];

        User::insert($users);
        
        OrganizationRoleUser::firstOrCreate([
                                    'organization_id' => 1,
                                    'user_id'         => 1,
                                    'role_id'         => 1
                                ]);
        OrganizationRoleUser::firstOrCreate([
                                    'organization_id' => 1,
                                    'user_id'         => 2,
                                    'role_id'         => 6
                                ]);
        $test_admin = USER::find(1);
        $test_admin->notify(new Welcome());

        $test_student = USER::find(2);
        $test_student->notify(new Welcome());
        
    }
}
