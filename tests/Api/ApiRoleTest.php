<?php

namespace Tests\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiRoleTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    public function an_unauthificated_client_can_not_get_roles() 
    {

        $response = $this->get('/api/v1/roles')->assertStatus(302);
        $this->contains('login');
    }

    /** @test 
     * Use Route: GET, /api/v1/roles
     */
    public function an_authificated_client_can_get_all_roles() 
    {

        $this->signInApiAdmin();

        $role = [
            [
                'id' => 1,
                'title' => 'Admin',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null
            ],
            [
                'id' => 2,
                'title' => 'Creator',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null
            ],
            [
                'id' => 3,
                'title' => 'Indexer',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null
            ],
            [
                'id' => 4,
                'title' => 'Schooladmin',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null
            ],
            [
                'id' => 5,
                'title' => 'Teacher',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null
            ],
            [
                'id' => 6,
                'title' => 'Student',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null
            ],
            [
                'id' => 7,
                'title' => 'Guest',
                'created_at' => '2019-04-15 19:13:32',
                'updated_at' => '2019-04-15 19:13:32',
                'deleted_at' => null
            ],
        ];

        $this->get('/api/v1/roles')
                ->assertStatus(200)
                ->assertJson($role);
    }

    /** @test 
     * Use Route: GET, /api/v1/roles/{id}
     */
    public function an_authificated_client_can_get_a_role() 
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/roles/1')
                ->assertStatus(200)
                ->assertJson([
                    'id' => 1,
                    'title' => 'Admin',
                    'created_at' => '2019-04-15 19:13:32',
                    'updated_at' => '2019-04-15 19:13:32',
                    'deleted_at' => null
        ]);
    }

}
