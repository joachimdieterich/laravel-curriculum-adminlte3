<?php

namespace Tests\Api;

use Tests\TestCase;
use App\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Facades\Tests\Setup\GroupFactory;
use Facades\Tests\Setup\UserFactory;


class ApiGroupTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function an_unauthificated_client_can_not_get_groups()
    {
          
        $response = $this->get('/api/v1/groups')->assertStatus(302);
        $this->contains('login');
    }
    
     /** @test 
     * Use Route: GET, /api/v1/groups
     */   
    public function an_authificated_client_can_get_all_groups()
    {
        
        $this->signInApiAdmin();
        
        $this->get('/api/v1/groups')
             ->assertStatus(200)
             ->assertJson(Group::all()->toArray()); 
    }
    
    /** @test 
     * Use Route: GET, /api/v1/groups/{id}
     */     
    public function an_authificated_client_can_get_an_group()
    { 
        $this->signInApiAdmin();

        $this->get("/api/v1/groups/1") 
             ->assertStatus(200)
             ->assertJson(Group::find(1)->toArray()); 
    }
    
    /** @test 
     * Use Route: POST, /api/v1/groups
     */     
    public function an_authificated_client_can_create_a_group()
    { 
        $this->signInApiAdmin();
        //$this->withoutExceptionHandling();
        $this->post("/api/v1/groups" , $attributes = factory('App\Group')->raw());
        
        $this->assertDatabaseHas('groups', $attributes);
    } 
    
    /** @test 
     * Use Route: PUT, /api/v1/groups/{id}
     */     
    public function an_authificated_client_can_update_a_group()
    { 
        $this->signInApiAdmin();
        
        $new_group = $this->post("/api/v1/groups" , $attributes = factory('App\Group')->raw()); 
        
        $this->put("/api/v1/groups/{$new_group->getData()->id}" , 
                $changed_attribute = ['title' => 'New Title']); 
        
        $changed_attribute = array_filter($changed_attribute);
        
        $this->get("/api/v1/groups/{$new_group->getData()->id}") 
             ->assertStatus(200)
             ->assertJson($changed_attribute); 
    }
    
    /** @test 
     * Use Route: DELETE, /api/v1/groups/{id}
     */     
    public function an_authificated_client_can_delete_a_group()
    { 
        $this->signInApiAdmin();
        
        $new_group = $this->post("/api/v1/groups" , $attributes = factory('App\Group')->raw()); 
        
        $this->delete("/api/v1/groups/{$new_group->getData()->id}"); 
        
        $this->assertDatabaseMissing('groups', $attributes);
    }
    
    /** @test 
     * Use Route: POST, /api/v1/groups/enrol
     */     
    public function an_authificated_client_can_enrol_groups_to_organizations()
    { 
        $this->signInApiAdmin();
        
        $group1 = GroupFactory::create();
        $group2 = GroupFactory::create();
        $user1 = UserFactory::create();
        $user2 = UserFactory::create();
       
        $this->put("/api/v1/groups/enrol", $enrolment_1 = ['user_id' => $user1->id,
                                                           'group_id' => $group1->id,
                                                          ]);
        $this->assertDatabaseHas('group_user', $enrolment_1);
        
        $this->put("/api/v1/groups/enrol", $enrolment_2 = ['user_id' => $user2->id,
                             'group_id' => $group2->id]);
        $this->assertDatabaseHas('group_user', $enrolment_2);
        
    }
    
    /** @test 
     * Use Route: GET, /api/v1/groups/{groups}/members
     */     
    public function an_authificated_client_can_get_group_members()
    { 
        $this->signInApiAdmin();
        
        $group1 = GroupFactory::create();
        $user1 = UserFactory::create();
        $user2 = UserFactory::create();
        $user3 = UserFactory::create();
        $user4 = UserFactory::create();
       
        $this->put("/api/v1/groups/enrol", $enrolment_1 = ['user_id' => $user1->id, 'group_id' => $group1->id]);
        $this->put("/api/v1/groups/enrol", $enrolment_2 = ['user_id' => $user2->id, 'group_id' => $group1->id]);
        $this->put("/api/v1/groups/enrol", $enrolment_3 = ['user_id' => $user3->id, 'group_id' => $group1->id]);
        $this->put("/api/v1/groups/enrol", $enrolment_4 = ['user_id' => $user4->id, 'group_id' => $group1->id]);
        
        $members = $group1->users->toArray();
        $this->get("/api/v1/groups/{$group1->id}/members")
                ->assertJson($members)
                ->assertStatus(200);
    }
    
    /** @test 
     * Use Route: DELETE, /api/v1/groups/expel
     */     
    public function an_authificated_client_can_expel_users_from_organizations()
    { 
       $this->signInApiAdmin();
        
        $group1 = GroupFactory::create();
        $group2 = GroupFactory::create();
        $user1 = UserFactory::create();
        $user2 = UserFactory::create();
        
        $this->put("/api/v1/groups/enrol", $enrolment_1 = ['user_id' => $user1->id, 'group_id' => $group1->id]);
        $this->assertDatabaseHas('group_user', $enrolment_1);
        
        $this->put("/api/v1/groups/enrol", $enrolment_2 =['user_id' => $user2->id, 'group_id' => $group2->id]);
        $this->assertDatabaseHas('group_user', $enrolment_2);
        
        $this->delete("/api/v1/groups/expel", $enrolment_1);
        $this->assertDatabaseMissing('group_user', $enrolment_1);
        
        $this->delete("/api/v1/groups/expel", $enrolment_2);
        $this->assertDatabaseMissing('group_user', $enrolment_2); 
    } 
    
}
