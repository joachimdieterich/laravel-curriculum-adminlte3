<?php

namespace Tests\Api;

use Tests\TestCase;
use App\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiRoleTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    public function an_unauthificated_client_can_not_get_roles() 
    {
        $this->get('/api/v1/roles')->assertStatus(302);
        $this->contains('login');
    }

    /** @test 
     * Use Route: GET, /api/v1/roles
     */
    public function an_authificated_client_can_get_all_roles() 
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/roles')
                ->assertStatus(200)
                ->assertJson(Role::all()->toArray());
    }

    /** @test 
     * Use Route: GET, /api/v1/roles/{id}
     */
    public function an_authificated_client_can_get_a_role() 
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/roles/1')
                ->assertStatus(200)
                ->assertJson(Role::find(1)->toArray());
    }

}
