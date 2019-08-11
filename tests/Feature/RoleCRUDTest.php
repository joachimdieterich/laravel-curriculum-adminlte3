<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Role;

class RoleCRUDTest extends TestCase
{
     use RefreshDatabase;
     
     public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }
    
    /** @test 
     * Use Route: GET|HEAD, roles, roles.index
     */
    public function an_administrator_see_roles() 
    { 
        
        $this->get("roles")       
             ->assertStatus(200);
             
        /* Use Datatables */
        $roles = Role::all();
        $this->get("roles/list")
             ->assertStatus(200)
             ->assertViewHasAll(compact($roles));
    }
    
    /** @test 
     * Use Route: POST, roles, roles.index
     */     
    public function an_administrator_create_a_role()
    { 
        
        $this->post("roles" , $attributes = factory('App\Role')->raw())
             ->assertStatus(302);
        
        $this->assertDatabaseHas('roles', ['title' => $attributes['title']]);
    }
    
    /** @test 
     * Use Route: POST, roles, roles.index
     */     
    public function an_administrator_get_create_view_for_roles()
    { 
        
        $this->get("roles/create")
             ->assertStatus(200);
    }
    
    /** @test 
     * Use Route: DELETE, roles/massDestroy roles.massDestroy
     */  
    public function an_administrator_can_mass_delete_roles()
    {        
        $this->post("roles" , $role1 = factory('App\Role')->raw());
        $ids[] = Role::where('title', $role1['title'])->first()->id;
       
        $this->post("roles" , $role2 = factory('App\Role')->raw());
        $ids[] = Role::where('title', $role2['title'])->first()->id;
        
        $this->delete("/roles/massDestroy" , $attributes = [
                    'ids' =>  $ids,
                ])->assertStatus(204);   
        
        foreach($ids AS $id){
            $this->assertSoftDeleted('roles', [
                'id' => $id
            ]);  
        }
    }
    
    /** @test 
     * Use Route: DELETE, roles/{role}, roles.destroy
     */  
    public function an_administrator_delete_a_role()
    {
         
        $this->post("roles" , $role1 = factory('App\Role')->raw());
        $id = Role::where('title', $role1['title'])->first()->id;
        
        $this->followingRedirects()
                ->delete("roles/". $id )
                ->assertStatus(200);
    }
    
    /** @test 
     * Use Route: GET|HEAD, roles/{role}, roles.show
     */
    public function an_administrator_see_details_of_a_role() 
    { 
        
        $this->post("roles" , $role1 = factory('App\Role')->raw());
        $role = Role::where('title', $role1['title'])->first();
        
        $this->get("roles/{$role->id}")       
             ->assertStatus(200)
             ->assertViewHasAll(compact($role));
    }
    
    /** @test 
     * Use Route: PUT|PATCH, roles/{role}, roles.update
     */
    public function an_administrator_update_a_role()
    {
        
        $this->post("roles" , $role1 = factory('App\Role')->raw());
        $role = Role::where('title', $role1['title'])->first()->toArray();
               
        $this->assertDatabaseHas('roles', $role);
        
        $this->patch("roles/". $role['id'] , $new_attributes = factory('App\Role')->raw());
        $role_edit = Role::where('title', $new_attributes['title'])->first()->toArray();
        
        $this->assertDatabaseHas('roles', $role_edit);
    }
    
    /** @test 
     * Use Route: GET|HEAD, roles/{role}/edit, roles.edit
     */     
    public function an_administrator_get_edit_view_for_roles()
    { 
        $this->post("roles" , $role1 = factory('App\Role')->raw());
        $role = Role::where('title', $role1['title'])->first();
        
        $this->get("roles/{$role->id}/edit")
             ->assertStatus(200)
             ->assertSessionHasAll(compact($role));
    }
   
}
