<?php

namespace Tests\Api;

use Tests\TestCase;
use App\OrganizationType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiOrganizationTypeTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    public function an_unauthificated_client_can_not_get_organization_types() 
    {
        $this->get('/api/v1/organizationtypes')->assertStatus(302);
        $this->contains('login');
    }

    /** @test 
     * Use Route: GET, /api/v1/organizations/types
     */
    public function an_authificated_client_can_get_all_organization_types() 
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/organizationtypes')
                ->assertStatus(200)
                ->assertJson(OrganizationType::all()->toArray()); 
    }

    /** @test 
     * Use Route: GET, /api/v1/organizations/types/{id}
     */
    public function an_authificated_client_can_get_a_state() 
    {
        $this->signInApiAdmin();
        
        $this->get('/api/v1/organizationtypes/2')
                ->assertStatus(200)
                ->assertJson(OrganizationType::find(2)->toArray());
    }

}
