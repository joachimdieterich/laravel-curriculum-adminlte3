<?php

namespace Tests\Api;

use App\OrganizationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiOrganizationTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test
     * Use Route: GET, /api/v1/organizationtypes
     */
    public function an_authenticated_client_can_not_get_organization_types()
    {
        $this->get('/api/v1/organizationtypes')->assertStatus(302);
        $this->stringContains('login');
    }

    /** @test
     * Use Route: GET, /api/v1/organizationtypes
     */
    public function an_authenticated_client_can_get_all_organization_types()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/organizationtypes')
                ->assertStatus(200)
                ->assertJson(OrganizationType::all()->toArray());
    }

    /** @test
     * Use Route: GET, /api/v1/organizationtypes/{id}
     */
    public function an_authenticated_client_can_get_a_state()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/organizationtypes/2')
                ->assertStatus(200)
                ->assertJson(OrganizationType::find(2)->toArray());
    }
}
