<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Organization;
use Facades\Tests\Setup\OrganizationFactory;

class ManageOrganizationTest extends TestCase
{
     use RefreshDatabase;
     
    /** @test */
    public function an_administrator_create_an_institution()
    {
        $this->signInAdmin();
        
        $this->followingRedirects()->post("admin/organizations" , $attributes = factory('App\Organization')->raw())
                ->assertStatus(200);
        
        $this->assertDatabaseHas('organizations', $attributes);
    }
    
    /** @test */
    public function an_administrator_update_an_institution()
    {
        $this->signInAdmin();
        /* add new organization */
        $this->followingRedirects()
                ->post("admin/organizations" , $attributes = factory('App\Organization')->raw())
                ->assertStatus(200);
        
        $this->assertDatabaseHas('organizations', $attributes);
        /* edit organization*/
        $this->followingRedirects()
                ->patch("admin/organizations/". Organization::where('title', '=', $attributes['title'])->first()->id , $new_attributes = factory('App\Organization')->raw())
                ->assertStatus(200);
        
        $this->assertDatabaseHas('organizations', $new_attributes);
    }
    
    /** @test */
    public function an_administrator_delete_an_institution()
    {
        $this->signInAdmin();
        
        /* add new organization */
        $org = OrganizationFactory::create();
        
        $this->followingRedirects()
                ->delete("admin/organizations/". $org->id )
                ->assertStatus(200);
    }
}
