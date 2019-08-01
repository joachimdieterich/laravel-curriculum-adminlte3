<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Curriculum;
use Facades\Tests\Setup\CurriculumFactory;

class TerminalObjectiveCRUDTest extends TestCase
{
     use RefreshDatabase;
     
     public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
        /* add curriculum needed by tests*/
        $this->post("curricula" , $attributes = factory('App\Curriculum')->raw());
        
    }
    
    /** @test 
     * Use Route: GET|HEAD, terminalObjectives, terminalObjectives.index
     */
    public function a_user_can_retrive_terminalObjectives() 
    { 

        //dd(\App\TerminalObjective::All());
    }

    /** @test 
    * Use Route: POST, terminalObjectives, terminalObjectives.store
    */     
    public function an_administrator_create_an_terminal_objective()
    { 
        
        $attributes = factory('App\TerminalObjective')->raw();
        
        $this->post("terminalObjectives" , $attributes);
        
        $this->assertDatabaseHas('terminal_objectives', $attributes);
    }
   
}
