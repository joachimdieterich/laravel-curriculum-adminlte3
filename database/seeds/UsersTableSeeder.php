<?php

use App\User;
use App\OrganizationRoleUser;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [[
            'id'             => 1,
            'username'       => 'Admin',
            'firstname'      => 'Global',
            'lastname'       => 'Admin',
            'email'          => 'admin@curriculumonline.de',
            'password'       => '$2y$10$imU.Hdz7VauIT3LIMCMbsOXvaaTQg6luVqkhfkBcsUd.SJW2XSRKO',
            'remember_token' => null,
            'created_at'     => '2019-04-15 19:13:32',
            'updated_at'     => '2019-04-15 19:13:32',
            'deleted_at'     => null,
        ]];

        User::insert($users);
        OrganizationRoleUser::firstOrCreate([
                                    'organization_id' => 1,
                                    'user_id'         => 1,
                                    'role_id'         => 1
                                ]);
        
    }
}
