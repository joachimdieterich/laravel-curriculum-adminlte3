<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CountriesTableSeeder::class,
            StatesTableSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionRoleTableSeeder::class,
            OrganizationsTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
        ]);
    }
}
