<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CountriesTableSeeder::class,
            StatesTableSeeder::class,
            OrganizationTypeTableSeeder::class,
            SubjectsTableSeeder::class,
            SubjectTypesTableSeeder::class,
            GradesTableSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionRoleTableSeeder::class,
            OrganizationsTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            PeriodesTableSeeder::class,
            GroupsTableSeeder::class,
            GroupUserTableSeeder::class,
            ContextsTableSeeder::class,
            SharingLevelsTableSeeder::class,
            StatusDefinitionsTableSeeder::class,
            TaskTableSeeder::class,
            LicensesTableSeeder::class,
            ContentsTableSeeder::class,
            ObjectiveTypeTableSeeder::class,
            CurriculumTypeTableSeeder::class,
            CurriculumTableSeeder::class,
            CurriculumGroupTableSeeder::class,
            MediaTableSeeder::class,
            LevelsTableSeeder::class,
            CategoriesTableSeeder::class,
            ExtendPermissionTableSeeder::class,
            ExtendPermissionRoleTableSeeder::class
        ]);
    }
}
