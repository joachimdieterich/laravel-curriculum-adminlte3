<?php
namespace Database\Seeders;
use App\Notifications\Welcome;
use App\OrganizationRoleUser;
use App\User;
use Illuminate\Database\Seeder;

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
                'common_name'    => 'cn_Creator',
                'username'       => 'Creator',
                'firstname'      => 'Creator',
                'lastname'       => 'Role',
                'email'          => 'creator@curriculumonline.de',
                'password'       => '$2y$10$imU.Hdz7VauIT3LIMCMbsOXvaaTQg6luVqkhfkBcsUd.SJW2XSRKO',
                'remember_token' => null,
                'created_at'     => '2019-04-15 19:13:32',
                'updated_at'     => '2019-04-15 19:13:32',
                'deleted_at'     => null,
                'current_organization_id'     => 1,
            ],
            [
                'id'             => 3,
                'common_name'    => 'cn_Indexer',
                'username'       => 'Indexer',
                'firstname'      => 'Indexer',
                'lastname'       => 'Role',
                'email'          => 'indexer@curriculumonline.de',
                'password'       => '$2y$10$imU.Hdz7VauIT3LIMCMbsOXvaaTQg6luVqkhfkBcsUd.SJW2XSRKO',
                'remember_token' => null,
                'created_at'     => '2019-04-15 19:13:32',
                'updated_at'     => '2019-04-15 19:13:32',
                'deleted_at'     => null,
                'current_organization_id'     => 1,
            ],
            [
                'id'             => 4,
                'common_name'    => 'cn_Schooladmin',
                'username'       => 'Schooladmin',
                'firstname'      => 'Schooladmin',
                'lastname'       => 'Role',
                'email'          => 'schooladmin@curriculumonline.de',
                'password'       => '$2y$10$imU.Hdz7VauIT3LIMCMbsOXvaaTQg6luVqkhfkBcsUd.SJW2XSRKO',
                'remember_token' => null,
                'created_at'     => '2019-04-15 19:13:32',
                'updated_at'     => '2019-04-15 19:13:32',
                'deleted_at'     => null,
                'current_organization_id'     => 1,
            ],
            [
                'id'             => 5,
                'common_name'    => 'cn_Teacher',
                'username'       => 'Teacher',
                'firstname'      => 'Teacher',
                'lastname'       => 'Role',
                'email'          => 'teacher@curriculumonline.de',
                'password'       => '$2y$10$imU.Hdz7VauIT3LIMCMbsOXvaaTQg6luVqkhfkBcsUd.SJW2XSRKO',
                'remember_token' => null,
                'created_at'     => '2019-04-15 19:13:32',
                'updated_at'     => '2019-04-15 19:13:32',
                'deleted_at'     => null,
                'current_organization_id'     => 1,
            ],
            [
                'id'             => 6,
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
            ],
            [
                'id'             => 7,
                'common_name'    => 'cn_Parent',
                'username'       => 'Parent',
                'firstname'      => 'Parent',
                'lastname'       => 'Role',
                'email'          => 'parent@curriculumonline.de',
                'password'       => '$2y$10$imU.Hdz7VauIT3LIMCMbsOXvaaTQg6luVqkhfkBcsUd.SJW2XSRKO',
                'remember_token' => null,
                'created_at'     => '2019-04-15 19:13:32',
                'updated_at'     => '2019-04-15 19:13:32',
                'deleted_at'     => null,
                'current_organization_id'     => 1,
            ],
            [
                'id'             => 8,
                'common_name'    => 'cn_Guest',
                'username'       => 'Guest',
                'firstname'      => 'Guest',
                'lastname'       => 'Role',
                'email'          => 'guest@curriculumonline.de',
                'password'       => '$2y$10$imU.Hdz7VauIT3LIMCMbsOXvaaTQg6luVqkhfkBcsUd.SJW2XSRKO',
                'remember_token' => null,
                'created_at'     => '2019-04-15 19:13:32',
                'updated_at'     => '2019-04-15 19:13:32',
                'deleted_at'     => null,
                'current_organization_id'     => 1,
            ],
        ];

        User::insert($users);

        OrganizationRoleUser::firstOrCreate([
            'organization_id' => 1,
            'user_id'         => 1,
            'role_id'         => 1,
        ]);
        OrganizationRoleUser::firstOrCreate([
            'organization_id' => 1,
            'user_id'         => 2,
            'role_id'         => 2,
        ]);
        OrganizationRoleUser::firstOrCreate([
            'organization_id' => 1,
            'user_id'         => 3,
            'role_id'         => 3,
        ]);
        OrganizationRoleUser::firstOrCreate([
            'organization_id' => 1,
            'user_id'         => 4,
            'role_id'         => 4,
        ]);
        OrganizationRoleUser::firstOrCreate([
            'organization_id' => 1,
            'user_id'         => 5,
            'role_id'         => 5,
        ]);
        OrganizationRoleUser::firstOrCreate([
            'organization_id' => 1,
            'user_id'         => 6,
            'role_id'         => 6,
        ]);
        OrganizationRoleUser::firstOrCreate([
            'organization_id' => 1,
            'user_id'         => 7,
            'role_id'         => 7,
        ]);
        OrganizationRoleUser::firstOrCreate([
            'organization_id' => 1,
            'user_id'         => 8,
            'role_id'         => 8,
        ]);
        $test_admin = USER::find(1);
        $test_admin->notify(new Welcome());

        $test_creator = USER::find(2);
        $test_creator->notify(new Welcome());
        $test_indexer = USER::find(3);
        $test_indexer->notify(new Welcome());
        $test_schooladmin = USER::find(4);
        $test_schooladmin->notify(new Welcome());
        $test_teacher = USER::find(5);
        $test_teacher->notify(new Welcome());
        $test_student = USER::find(6);
        $test_student->notify(new Welcome());
        $test_parent = USER::find(7);
        $test_parent->notify(new Welcome());
        $test_guest = USER::find(8);
        $test_guest->notify(new Welcome());
    }
}
