<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Grade;
use Facades\Tests\Setup\OrganizationFactory;

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
