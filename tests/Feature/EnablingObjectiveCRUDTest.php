<?php

namespace Tests\Feature;

use Tests\TestCase;
use Facades\Tests\Setup\EnablingObjectiveFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EnablingObjectiveCRUDTest extends TestCase
{
     use RefreshDatabase;
     
     public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
        /* add curriculum needed by tests*/
        $this->post("curricula" , $attributes = factory('App\Curriculum')->raw());
        $this->post("terminalObjectives" , factory('App\TerminalObjective')->raw());
        
    }
    
 
    /** @test 
    * Use Route: POST, enablingObjectives, enablingObjectives.store
    */     
    public function an_administrator_create_an_enabling_objective()
    { 
        $attributes = factory('App\EnablingObjective')->raw();
        
        $this->post("enablingObjectives" , $attributes);
        
        $this->assertDatabaseHas('enabling_objectives', $attributes); 
    }
    
    /** @test 
     * Use Route: GET|HEAD, enablingObjectives/{enablingObjective}, enablingObjectives.show
     */
    public function an_administrator_see_details_of_an_enablingObjective() 
    { 
        $enablingObjective = EnablingObjectiveFactory::create();
        $this->get("enablingObjectives/{$enablingObjective->id}")       
             ->assertStatus(200)
             ->assertViewHasAll(compact($enablingObjective));
    }
   
}
