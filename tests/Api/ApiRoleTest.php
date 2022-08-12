<?php

namespace Tests\Api;

use App\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_client_can_not_get_roles()
    {
        $this->get('/api/v1/roles')->assertStatus(302);
        $this->stringContains('login');
    }

    /** @test
     * Use Route: GET, /api/v1/roles
     */
    public function an_authenticated_client_can_get_all_roles()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/roles')
                ->assertStatus(200)
                ->assertJson(Role::all()->toArray());
    }

    /** @test
     * Use Route: GET, /api/v1/roles/{id}
     */
    public function an_authenticated_client_can_get_a_role()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/roles/1')
                ->assertStatus(200)
                ->assertJson(Role::find(1)->toArray());
    }
}
