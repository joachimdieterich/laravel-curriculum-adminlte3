<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\OrganizationType;
use Facades\Tests\Setup\OrganizationTypeFactory;

class OrganizationTypeCRUDTest extends TestCase
{
     use RefreshDatabase;
     
     public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }
    
    /** @test 
     * Use Route: GET|HEAD, organizationtypes, organizationtypes.index
     */
    public function an_administrator_see_organization_types() 
    { 
        
        $this->get("organizationtypes")       
             ->assertStatus(200);
        
        /* Use Datatables */
        $organizationTypes = OrganizationType::first();
        $this->get("organizationtypes/list")
             ->assertStatus(200)
             ->assertViewHasAll(compact($organizationTypes));
    }
    
    /** @test 
     * Use Route: POST, organizationtypes, organizationtypes.index
     */     
    public function an_administrator_create_an_organization_type()
    {       
        $attributes = factory('App\OrganizationType')->raw();
        
        $this->post("organizationtypes" , $attributes);
 
        $this->assertDatabaseHas('organization_types', OrganizationType::where('title', $attributes['title'])->get()->first()->toArray());
    }
    
    /** @test 
     * Use Route: POST, organizationtypes, organizationtypes.create
     */     
    public function an_administrator_get_create_view_for_organization()
    { 
        $this->get("organizationtypes/create")
             ->assertStatus(200);
    }
    
    
    /** @test 
     * Use Route: DELETE, organizationtypes/{organizationtype}, organizationtypes.destroy
     */  
    public function an_administrator_delete_an_organization_type()
    {
         
        /* add new organization */
        $org_type = OrganizationTypeFactory::create();
        
        $this->followingRedirects()
                ->delete("organizationtypes/". $org_type->id )
                ->assertStatus(200);
    }
    
    /** @test 
     * Use Route: GET|HEAD, organizationtypes/{organizationtype}, organizationtypes.show
     */
    public function an_administrator_see_details_of_an_organization_types() 
    { 
        $org_type = OrganizationTypeFactory::create();
       
        $this->get("organizationtypes/{$org_type->id}")       
             ->assertStatus(200)
             ->assertViewHasAll(compact($org_type));
    }
    
    /** @test 
     * Use Route: PUT|PATCH, organizationtypes/{organizationtype}, organizationtypes.update
     */
    public function an_administrator_update_an_organization_types()
    { 
        $this->post("organizationtypes" , $attributes = factory('App\OrganizationType')->raw());
        
        $this->assertDatabaseHas('organization_types', $attributes);
        /* edit organization*/
        $this->patch("organizationtypes/". OrganizationType::where('title', '=', $attributes['title'])->first()->id , $new_attributes = factory('App\OrganizationType')->raw());
        
        $this->assertDatabaseHas('organization_types', $new_attributes);
    }
    
    /** @test 
     * Use Route: GET|HEAD, organizationtypes/{organizationtype}/edit, organizations.edit
     */     
    public function an_administrator_get_edit_view_for_organization_type()
    { 
        $org_type = OrganizationTypeFactory::create();
        
        $this->get("organizationtypes/{$org_type->id}/edit")
             ->assertStatus(200)
             ->assertSessionHasAll(compact($org_type));
    }
   
}
