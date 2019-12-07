<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Facades\Tests\Setup\GroupFactory;
use Facades\Tests\Setup\CurriculumFactory;

class CurriculumEnrollmentTest extends TestCase
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
    public function an_administrator_can_enrol_multiple_groups_to_existing_curricula()
    {
        $group1 = GroupFactory::create();
        $group2 = GroupFactory::create();
        $curriculum1 = CurriculumFactory::create();
        $curriculum2 = CurriculumFactory::create();
        
        $enrollment_list = [
                            ['curriculum_id' => $curriculum1->id,
                             'group_id' => $group1->id,
                            ],
                            ['curriculum_id' => $curriculum1->id,
                             'group_id' => $group2->id,
                            ],
                            ['curriculum_id' => $curriculum2->id,
                             'group_id' => $group1->id,
                            ],
                            ['curriculum_id' => $curriculum2->id,
                             'group_id' => $group2->id,
                            ]       
                          ];
                
        
        $this->post("curricula/enrol" , $attributes = [
                    'enrollment_list' => $enrollment_list,
                ]);
        
        foreach($enrollment_list AS $entry){
            $this->assertDatabaseHas('curriculum_group', [
            'curriculum_id' => $entry['curriculum_id'], 
            'group_id' => $entry['group_id'], 
            ]);
        }
     
    }
    
    /** @test */
    public function an_administrator_can_expel_multiple_groups_to_existing_curricula()
    {
        $group1 = GroupFactory::create();
        $group2 = GroupFactory::create();
        $curriculum1 = CurriculumFactory::create();
        $curriculum2 = CurriculumFactory::create();
        
        $enrollment_list = [
                            ['curriculum_id' => $curriculum1->id,
                             'group_id' => $group1->id,
                            ],
                            ['curriculum_id' => $curriculum1->id,
                             'group_id' => $group2->id,
                            ],
                            ['curriculum_id' => $curriculum2->id,
                             'group_id' => $group1->id,
                            ],
                            ['curriculum_id' => $curriculum2->id,
                             'group_id' => $group2->id,
                            ]       
                          ];
                
        
        $this->post("curricula/enrol" , $attributes = [
                    'enrollment_list' => $enrollment_list,
                ]);
        
        foreach($enrollment_list AS $entry){
            $this->assertDatabaseHas('curriculum_group', [
            'curriculum_id' => $entry['curriculum_id'], 
            'group_id' => $entry['group_id'], 
            ]);
        }

        /* expel */
        $this->delete("curricula/expel" , ['expel_list' => $enrollment_list]);
        
        foreach($enrollment_list AS $entry){
            $this->assertDatabaseMissing('curriculum_group', [
            'curriculum_id' => $entry['curriculum_id'], 
            'group_id' => $entry['group_id'], 
            ]);
        }
        
    }
    
}
