<?php

namespace Tests\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Facades\Tests\Setup\OrganizationFactory;
use Facades\Tests\Setup\UserFactory;

class ApiOrganizationTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function an_unauthificated_client_can_not_get_organizations()
    {
          
        $response = $this->get('/api/v1/organizations')->assertStatus(302);
        $this->contains('login');
    }
    
     /** @test 
     * Use Route: GET, /api/v1/organizations
     */   
    public function an_authificated_client_can_get_all_organizations()
    {
        
        $this->signInApiAdmin();
        
        $organization = [[
            'id'             => 1,
            'title'          => 'curriculumonline',
            'description'    => 'Admins Institution',
            'street'         => 'Demostreet 1',
            'postcode'       => '12345',
            'city'           => 'Ilbesheim bei Landau in der Pfalz',
            'state_id'       => 'DE-RP',
            'country_id'     => 'DE',
            'phone'          => '0123-456789',
            'email'          => 'mail@curriculumonline.de',
            'organization_type_id' => 1,
            'status_id'         => 1,
            'created_at'     => '2019-04-15 19:13:32',
            'updated_at'     => '2019-04-15 19:13:32',
        ]];
        
        $this->get('/api/v1/organizations')
             ->assertStatus(200)
             ->assertJson($organization); 
    }
    
    /** @test 
     * Use Route: GET, /api/v1/organizations/{id}
     */     
    public function an_authificated_client_can_get_an_organization()
    { 
        $this->signInApiAdmin();
        
        $this->post("/api/v1/organizations" , $attributes = factory('App\Organization')->raw()); //create new organization with ID 2, ID 1 exists seeded

        $this->get('/api/v1/organizations/2') 
             ->assertStatus(200)
             ->assertJson($attributes); 
    }
    
    /** @test 
     * Use Route: POST, /api/v1/organizations
     */     
    public function an_authificated_client_can_create_an_organization()
    { 
        $this->signInApiAdmin();
        //$this->withoutExceptionHandling();
        $this->post("/api/v1/organizations" , $attributes = factory('App\Organization')->raw());
        
        $this->assertDatabaseHas('organizations', $attributes);
    } 
    
    /** @test 
     * Use Route: PUT, /api/v1/organizations/{id}
     */     
    public function an_authificated_client_can_update_an_organization()
    { 
        $this->signInApiAdmin();
        //$this->withoutExceptionHandling();
        $this->post("/api/v1/organizations" , $attributes = factory('App\Organization')->raw()); //create new organization with ID 2, ID 1 exists seeded
        
        $this->put("/api/v1/organizations/2" , $changed_attribute = ['title' => 'New Title',
                                                                     'status_id' => null]); 
        
        $changed_attribute = array_filter($changed_attribute);
        
        $this->get('/api/v1/organizations/2') 
             ->assertStatus(200)
             ->assertJson($changed_attribute); 
    }
    
    /** @test 
     * Use Route: DELETE, /api/v1/organizations/{id}
     */     
    public function an_authificated_client_can_delete_an_organization()
    { 
        $this->signInApiAdmin();
        
        $this->post("/api/v1/organizations" , $attributes = factory('App\Organization')->raw()); //create new organization with ID 2, ID 1 exists seeded
        
        $this->delete("/api/v1/organizations/2"); 
        
        $this->assertDatabaseMissing('organizations', $attributes);
    }
    
    /** @test 
     * Use Route: POST, /api/v1/organizations/enrol
     */     
    public function an_authificated_client_can_enrol_users_to_organizations()
    { 
        $this->signInApiAdmin();
        
        $organization1 = OrganizationFactory::create();
        
        $user1 = UserFactory::create();
        $user2 = UserFactory::create();
        
       
        $this->put("/api/v1/organizations/enrol", $enrolment_1 = ['user_id' => $user1->id,
                                                    'organization_id' => $organization1->id,
                                                    'role_id' => 1                             // 1 == Admin
                                                   ]);
        $this->assertDatabaseHas('organization_role_users', $enrolment_1);
        
        $this->put("/api/v1/organizations/enrol", $enrolment_2 =['user_id' => $user2->id,
                             'organization_id' => $organization1->id,
                             'role_id' => 4                             // 4 == Schooladmin
                            ]);
        $this->assertDatabaseHas('organization_role_users', $enrolment_2);
        
    } 
    
    /** @test 
     * Use Route: DELETE, /api/v1/organizations/expel
     */     
    public function an_authificated_client_can_expel_users_from_organizations()
    { 
        $this->signInApiAdmin();
        
        $organization1 = OrganizationFactory::create();
        
        $user1 = UserFactory::create();
        $user2 = UserFactory::create();
        
       
        $this->put("/api/v1/organizations/enrol", $enrolment_1 = ['user_id' => $user1->id,
                                                    'organization_id' => $organization1->id,
                                                    'role_id' => 1                             // 1 == Admin
                                                   ]);
        $this->assertDatabaseHas('organization_role_users', $enrolment_1);
        
        $this->put("/api/v1/organizations/enrol", $enrolment_2 =['user_id' => $user2->id,
                             'organization_id' => $organization1->id,
                             'role_id' => 4                             // 4 == Schooladmin
                            ]);
        $this->assertDatabaseHas('organization_role_users', $enrolment_2);
        
        $this->delete("/api/v1/organizations/expel", ['user_id' => $user1->id,
                                                    'organization_id' => $organization1->id,
                                                   ]);
        $this->assertDatabaseMissing('organization_role_users', $enrolment_1);
        
        $this->delete("/api/v1/organizations/expel", ['user_id' => $user2->id,
                                                    'organization_id' => $organization1->id,
                                                   ]);
        $this->assertDatabaseMissing('organization_role_users', $enrolment_2);
        
       
    } 
}
