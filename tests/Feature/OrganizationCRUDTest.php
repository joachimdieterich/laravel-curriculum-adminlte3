<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Organization;
use Facades\Tests\Setup\OrganizationFactory;

class OrganizationCRUDTest extends TestCase
{
     use RefreshDatabase;
     
     public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }
    
    /** @test 
     * Use Rout: GET|HEAD, admin/organizations, admin.organizations.index
     */
    public function an_administrator_see_organizations() 
    { 
        
        $this->get("admin/organizations")       
             ->assertStatus(200);
        
        /* Use Datatables */
        $organization = Organization::first();
        $this->get("admin/organizationList")
             ->assertStatus(200)
             ->assertViewHasAll(compact($organization));
    }
    
    /** @test 
     * Use Rout: POST, admin/organizations, admin.organizations.index
     */     
    public function an_administrator_create_an_organization()
    { 
        
        $this->post("admin/organizations" , $attributes = factory('App\Organization')->raw());
        
        $this->assertDatabaseHas('organizations', $attributes);
    }
    
    /** @test 
     * Use Rout: POST, admin/organizations, admin.organizations.index
     */     
    public function an_administrator_get_create_view_for_organization()
    { 
        
        $this->get("admin/organizations/create")
             ->assertStatus(200);
    }
    
    /** @test 
     * Use Rout: DELETE, admin/organizations/destroy admin.organizations.massDestroy
     */  
    public function an_administrator_can_mass_delete_organizations()
    {        
        $orgs = factory(Organization::class, 50)->create();
        $ids = $orgs->pluck('id')->toArray();
        
        $this->delete("/admin/organizations/massDestroy" , $attributes = [
                    'ids' =>  $ids,
                ])->assertStatus(204);   
        
        foreach($ids AS $id){
            
            $this->assertDatabaseMissing('organizations', [
                'id' => $id
            ]);  
        }
    }
    
    /** @test 
     * Use Rout: DELETE, admin/organizations/{organization}, admin.organizations.destroy
     */  
    public function an_administrator_delete_an_organization()
    {
         
        /* add new organization */
        $org = OrganizationFactory::create();
        
        $this->followingRedirects()
                ->delete("admin/organizations/". $org->id )
                ->assertStatus(200);
    }
    
    /** @test 
     * Use Rout: GET|HEAD, admin/organizations/{organization}, admin.organizations.show
     */
    public function an_administrator_see_details_of_an_organizations() 
    { 
        
        $org = OrganizationFactory::create();
        
        $this->get("admin/organizations/{$org->id}")       
             ->assertStatus(200)
             ->assertViewHasAll(compact($org));
    }
    
    /** @test 
     * Use Rout: PUT|PATCH, admin/organizations/{organization}, admin.organizations.update
     */
    public function an_administrator_update_an_organization()
    {
        
        $this->post("admin/organizations" , $attributes = factory('App\Organization')->raw());
        
        $this->assertDatabaseHas('organizations', $attributes);
        /* edit organization*/
        $this->patch("admin/organizations/". Organization::where('title', '=', $attributes['title'])->first()->id , $new_attributes = factory('App\Organization')->raw());
        
        $this->assertDatabaseHas('organizations', $new_attributes);
    }
    
    /** @test 
     * Use Rout: GET|HEAD, admin/organizations/{organization}/edit, admin.organizations.edit
     */     
    public function an_administrator_get_edit_view_for_organization()
    { 
        $org = OrganizationFactory::create();
        
        $this->get("admin/organizations/{$org->id}/edit")
             ->assertStatus(200)
             ->assertSessionHasAll(compact($org));
    }
   
}
