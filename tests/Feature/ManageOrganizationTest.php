<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Organization;

class ManageOrganizationTest extends TestCase
{
     use RefreshDatabase;
     
     public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }
    
    
    /** @test */
    public function an_administrator_update_the_status_of_an_organization()
    {
        
        /* add new organization */
        $this->post("admin/organizations" , $attributes = factory('App\Organization')->raw());
        
        $this->assertDatabaseHas('organizations', $attributes);
        
        /* edit organization */ 
        $attributes['status_id'] = 2;
        
        $this->patch("admin/organizations/". (Organization::where(
                        'title', '=', $attributes['title'])->first()->id) , 
                        ['status_id' => 2] );
        
        $this->assertDatabaseHas('organizations', $attributes);
    }
    
    
    
    
}
