<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Facades\Tests\Setup\GroupFactory;
use Facades\Tests\Setup\UserFactory;

class GroupEnrollmentTest extends TestCase
{
     use RefreshDatabase;
     
    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }
    
    
    /** @test 
     * 
     * Use Route: POST, groups/enrol, groups.enrol
     */
    public function an_administrator_can_enrol_multiple_users_to_existing_groups()
    {
        $group1 = GroupFactory::create();
        $group2 = GroupFactory::create();
        $user1 = UserFactory::create();
        $user2 = UserFactory::create();
        
        $enrollment_list = [
                            ['user_id' => $user1->id,
                             'group_id' => $group1->id,
                            ],
                            ['user_id' => $user1->id,
                             'group_id' => $group2->id,
                            ],
                            ['user_id' => $user2->id,
                             'group_id' => $group1->id,
                            ],
                            ['user_id' => $user2->id,
                             'group_id' => $group2->id,
                            ]       
                          ];
                
        
        $this->post("groups/enrol" , $attributes = [
                    'enrollment_list' => $enrollment_list,
                ]);
        
        foreach($enrollment_list AS $entry){
            $this->assertDatabaseHas('group_user', [
            'user_id' => $entry['user_id'], 
            'group_id' => $entry['group_id'], 
            ]);
        }
     
    }
    
    /** @test */
    public function an_administrator_can_expel_multiple_users_from_existing_groups()
    {
        $group1 = GroupFactory::create();
        $group2 = GroupFactory::create();
        $user1 = UserFactory::create();
        $user2 = UserFactory::create();
        
        $enrollment_list = [
                            ['user_id' => $user1->id,
                             'group_id' => $group1->id,
                            ],
                            ['user_id' => $user1->id,
                             'group_id' => $group2->id,
                            ],
                            ['user_id' => $user2->id,
                             'group_id' => $group1->id,
                            ],
                            ['user_id' => $user2->id,
                             'group_id' => $group2->id,
                            ]       
                          ];
                
        
        $this->post("groups/enrol" , $attributes = [
                    'enrollment_list' => $enrollment_list,
                ]);
        
        foreach($enrollment_list AS $entry){
            $this->assertDatabaseHas('group_user', [
            'user_id' => $entry['user_id'], 
            'group_id' => $entry['group_id'], 
            ]);
        }

        /* expel */
        $this->delete("groups/expel" , ['expel_list' => $enrollment_list]);
        
        foreach($enrollment_list AS $entry){
            $this->assertDatabaseMissing('group_user', [
            'user_id' => $entry['user_id'], 
            'group_id' => $entry['group_id'], 
            ]);
        }
        
    }
    
}
