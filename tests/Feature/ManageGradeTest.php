<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageGradeTest extends TestCase
{
     use RefreshDatabase;
     
    /** @test */
    public function an_administrator_see_grades()
    {
        $this->signInAdmin();
        
        $this->followingRedirects()->get("admin/grades")
            ->assertStatus(200);
    }
    
   
    
}
