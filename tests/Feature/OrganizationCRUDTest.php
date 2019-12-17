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
     * Use Route: GET|HEAD, organizations, organizations.index
     */
    public function an_administrator_see_organizations() 
    { 
        
        $this->get("organizations")       
             ->assertStatus(200);
        
        /* Use Datatables */
        $organization = Organization::first();
        $this->get("organizations/list")
             ->assertStatus(200)
             ->assertViewHasAll(compact($organization));
    }
    
    /** @test 
     * Use Route: POST, organizations, organizations.store
     */     
    public function an_administrator_create_an_organization()
    { 
        
        $this->post("organizations" , $attributes = factory('App\Organization')->raw());
        
        $this->assertDatabaseHas('organizations', $attributes);
    }
    
    /** @test 
     * Use Route: POST, organizations, organizations.create
     */     
    public function an_administrator_get_create_view_for_organization()
    { 
        
        $this->get("organizations/create")
             ->assertStatus(200);
    }
    
    /** @test 
     * Use Route: DELETE, organizations/massDestroy organizations.massDestroy
     */  
    public function an_administrator_can_mass_delete_organizations()
    {        
        $orgs = factory(Organization::class, 50)->create();
        $ids = $orgs->pluck('id')->toArray();
        
        $this->delete("/organizations/massDestroy" , $attributes = [
                    'ids' =>  $ids,
                ])->assertStatus(204);   
        
        foreach($ids AS $id){
            
            $this->assertDatabaseMissing('organizations', [
                'id' => $id
            ]);  
        }
    }
    
    /** @test 
     * Use Route: DELETE, organizations/{organization}, organizations.destroy
     */  
    public function an_administrator_delete_an_organization()
    {
         
        /* add new organization */
        $org = OrganizationFactory::create();
        
        $this->followingRedirects()
                ->delete("organizations/". $org->id )
                ->assertStatus(200);
    }
    
    /** @test 
     * Use Route: GET|HEAD, organizations/{organization}, organizations.show
     */
    public function an_administrator_see_details_of_an_organizations() 
    { 
        
        $org = OrganizationFactory::create();
        
        $this->get("organizations/{$org->id}")       
             ->assertStatus(200)
             ->assertViewHasAll(compact($org));
    }
    
    /** @test 
     * Use Route: PUT|PATCH, organizations/{organization}, organizations.update
     */
    public function an_administrator_update_an_organization()
    {
        
        $this->post("organizations" , $attributes = factory('App\Organization')->raw());
        
        $this->assertDatabaseHas('organizations', $attributes);
        /* edit organization*/
        $this->patch("organizations/". Organization::where('title', '=', $attributes['title'])->first()->id , $new_attributes = factory('App\Organization')->raw());
        
        $this->assertDatabaseHas('organizations', $new_attributes);
    }
    
    /** @test 
     * Use Route: GET|HEAD, organizations/{organization}/edit, organizations.edit
     */     
    public function an_administrator_get_edit_view_for_organization()
    { 
        $org = OrganizationFactory::create();
        
        $this->get("organizations/{$org->id}/edit")
             ->assertStatus(200)
             ->assertSessionHasAll(compact($org));
    }
   
}
