<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Permission;

class PermissionCRUDTest extends TestCase
{
     use RefreshDatabase;
     
     public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }
    
    /** @test 
     * Use Route: GET|HEAD, permissions, permissions.index
     */
    public function an_administrator_see_permissions() 
    { 
        $permissions = Permission::all();
        $this->get("permissions")       
             ->assertStatus(200)
             ->assertViewHasAll(compact($permissions));
    }
    
    /** @test 
     * Use Route: POST, permissions, permissions.index
     */     
    public function an_administrator_create_a_permission()
    { 
        
        $this->post("permissions" , $attributes = factory('App\Permission')->raw())
             ->assertStatus(302);
        
        $this->assertDatabaseHas('permissions', ['title' => $attributes['title']]);
    }
    
    /** @test 
     * Use Route: POST, permissions, permissions.index
     */     
    public function an_administrator_get_create_view_for_permissions()
    { 
        
        $this->get("permissions/create")
             ->assertStatus(200);
    }
    
    
    /** @test 
     * Use Route: DELETE, permissions/{permission}, permissions.destroy
     */  
    public function an_administrator_delete_a_permission()
    {
         
        $this->post("permissions" , $permission1 = factory('App\Permission')->raw());
        $id = Permission::where('title', $permission1['title'])->first()->id;
        
        $this->followingRedirects()
                ->delete("permissions/". $id )
                ->assertStatus(200);
    }
    
    /** @test 
     * Use Route: GET|HEAD, permissions/{permission}, permissions.show
     */
    public function an_administrator_see_details_of_a_permission() 
    { 
        
        $this->post("permissions" , $permission1 = factory('App\Permission')->raw());
        $permission = Permission::where('title', $permission1['title'])->first();
        
        $this->get("permissions/{$permission->id}")       
             ->assertStatus(200)
             ->assertViewHasAll(compact($permission));
    }
    
    /** @test 
     * Use Route: PUT|PATCH, permissions/{permission}, permissions.update
     */
    public function an_administrator_update_a_permission()
    {
        
        $this->post("permissions" , $permission1 = factory('App\Permission')->raw());
        $permission = Permission::where('title', $permission1['title'])->first()->toArray();
               
        $this->assertDatabaseHas('permissions', $permission);
        
        $this->patch("permissions/". $permission['id'] , $new_attributes = factory('App\Permission')->raw());
        $permission_edit = Permission::where('title', $new_attributes['title'])->first()->toArray();
        
        $this->assertDatabaseHas('permissions', $permission_edit);
    }
    
    /** @test 
     * Use Route: GET|HEAD, permissions/{permission}/edit, permissions.edit
     */     
    public function an_administrator_get_edit_view_for_permissions()
    { 
        $this->post("permissions" , $permission1 = factory('App\Permission')->raw());
        $permission = Permission::where('title', $permission1['title'])->first();
        
        $this->get("permissions/{$permission->id}/edit")
             ->assertStatus(200)
             ->assertSessionHasAll(compact($permission));
    }
   
}
