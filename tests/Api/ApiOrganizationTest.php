<?php

namespace Tests\Api;

use App\Organization;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiOrganizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test
     * Use Route: GET, /api/v1/organizations
     */
    public function an_authenticated_client_can_not_get_organizations()
    {
        $this->get('/api/v1/organizations')->assertStatus(302);
        $this->stringContains('login');
    }

    /** @test
     * Use Route: GET, /api/v1/organizations
     */
    public function an_authenticated_client_can_get_all_organizations()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/organizations')
             ->assertStatus(200)
             ->assertJson(Organization::all()->toArray());
    }

    /** @test
     * Use Route: GET, /api/v1/organizations/{id}
     */
    public function an_authenticated_client_can_get_an_organization()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/organizations/1')
             ->assertStatus(200)
             ->assertJson(Organization::find(1)->toArray());
    }

    /** @test
     * Use Route: POST, /api/v1/organizations
     */
    public function an_authenticated_client_can_create_an_organization()
    {
        $this->signInApiAdmin();

        $this->post('/api/v1/organizations', $attributes = Organization::factory()->raw());

        $this->assertDatabaseHas('organizations', $attributes);
    }

    /** @test
     * Use Route: PUT, /api/v1/organizations/{id}
     */
    public function an_authenticated_client_can_update_an_organization()
    {
        $this->signInApiAdmin();

        $new_organization = $this->post('/api/v1/organizations', $attributes = Organization::factory()->raw());

        $this->put("/api/v1/organizations/{$new_organization->getData()->id}", $changed_attribute = ['title' => 'New Title',
            'status_id' => null, ]);

        $changed_attribute = array_filter($changed_attribute);

        $this->get("/api/v1/organizations/{$new_organization->getData()->id}")
             ->assertStatus(200)
             ->assertJson($changed_attribute);
    }

    /** @test
     * Use Route: DELETE, /api/v1/organizations/{id}
     */
    public function an_authenticated_client_can_delete_an_organization()
    {
        $this->signInApiAdmin();

        $this->post('/api/v1/organizations', $attributes = Organization::factory()->raw()); //create new organization with ID 2, ID 1 exists seeded

        $this->delete('/api/v1/organizations/2');

        $this->assertDatabaseMissing('organizations', $attributes);
    }

    /** @test
     * Use Route: POST, /api/v1/organizations/enrol
     */
    public function an_authenticated_client_can_enrol_users_to_organizations()
    {
        $this->signInApiAdmin();

        $organization1 = Organization::factory()->create();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->put('/api/v1/organizations/enrol', $enrolment_1 = ['user_id' => $user1->id,
            'organization_id' => $organization1->id,
            'role_id' => 1,                             // 1 == Admin
        ]);
        $this->assertDatabaseHas('organization_role_users', $enrolment_1);

        $this->put('/api/v1/organizations/enrol', $enrolment_2 = ['user_id' => $user2->id,
            'organization_id' => $organization1->id,
            'role_id' => 4,                             // 4 == Schooladmin
        ]);
        $this->assertDatabaseHas('organization_role_users', $enrolment_2);
    }

    /** @test
     * Use Route: GET, /api/v1/organizations/{organization}/members
     */
    public function an_authenticated_client_can_get_organization_members()
    {
        $this->signInApiAdmin();

        $organization1 = Organization::factory()->create();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        $user4 = User::factory()->create();

        $this->put('/api/v1/organizations/enrol',
                [
                    'user_id' => $user1->id,
                    'organization_id' => $organization1->id,
                    'role_id' => 1,                             // 1 == Admin
                ]);

        $this->put('/api/v1/organizations/enrol',
                [
                    'user_id' => $user2->id,
                    'organization_id' => $organization1->id,
                    'role_id' => 2,                             // 2 == Creator
                ]);
        $this->put('/api/v1/organizations/enrol',
                [
                    'user_id' => $user3->id,
                    'organization_id' => $organization1->id,
                    'role_id' => 3,                             // 3 == Indexer
                ]);
        $this->put('/api/v1/organizations/enrol',
                [
                    'user_id' => $user4->id,
                    'organization_id' => $organization1->id,
                    'role_id' => 4,                             // 4 == Schooladmin
                ]);
        $members = $organization1->users->toArray();
        $this->get('/api/v1/organizations/'.$organization1->id.'/members')
                ->assertJson($members)
                ->assertStatus(200);
    }

    /** @test
     * Use Route: DELETE, /api/v1/organizations/expel
     */
    public function an_authenticated_client_can_expel_users_from_organizations()
    {
        $this->signInApiAdmin();

        $organization1 = Organization::factory()->create();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->put('/api/v1/organizations/enrol', $enrolment_1 = ['user_id' => $user1->id,
            'organization_id' => $organization1->id,
            'role_id' => 1,                             // 1 == Admin
        ]);
        $this->assertDatabaseHas('organization_role_users', $enrolment_1);

        $this->put('/api/v1/organizations/enrol', $enrolment_2 = ['user_id' => $user2->id,
            'organization_id' => $organization1->id,
            'role_id' => 4,                             // 4 == Schooladmin
        ]);
        $this->assertDatabaseHas('organization_role_users', $enrolment_2);

        $this->delete('/api/v1/organizations/expel', ['user_id' => $user1->id,
            'organization_id' => $organization1->id,
        ]);
        $this->assertDatabaseMissing('organization_role_users', $enrolment_1);

        $this->delete('/api/v1/organizations/expel', ['user_id' => $user2->id,
            'organization_id' => $organization1->id,
        ]);
        $this->assertDatabaseMissing('organization_role_users', $enrolment_2);
    }
}
