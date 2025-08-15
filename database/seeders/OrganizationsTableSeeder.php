<?php

namespace Database\Seeders;

use App\Organization;
use Illuminate\Database\Seeder;

class OrganizationsTableSeeder extends Seeder
{
    public function run()
    {
        $organizations = [[
            'id' => 1,
            'common_name' => 'CUR',
            'title' => 'curriculumonline',
            'description' => 'Admins Institution',
            'street' => 'Demostreet 1',
            'postcode' => '12345',
            'city' => 'Ilbesheim bei Landau in der Pfalz',
            'state_id' => 'DE-RP',
            'country_id' => 'DE',
            'phone' => '0123-456789',
            'email' => 'mail@curriculumonline.de',
            'organization_type_id' => 1,
            'status_id' => 1,
            'created_at' => '2019-04-15 19:13:32',
            'updated_at' => '2019-04-15 19:13:32',
        ]];

        Organization::insert($organizations);
    }
}
