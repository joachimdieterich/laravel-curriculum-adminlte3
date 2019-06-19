<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Group;

class GroupCRUDTest extends TestCase
{
    use RefreshDatabase;
     
    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }
    
    /** @test 
     * Use Route: GET|HEAD, admin/groups, admin.groups.index
     */
    public function an_administrator_see_groups() 
    { 
        $this->get("admin/groups")       
             ->assertStatus(200);
        
        /* Use Datatables */
        $groups = Group::first();
        $this->get("admin/groups/list")
             ->assertStatus(200)
             ->assertViewHasAll(compact($groups));
    }
    
    /** @test 
     * Use Route: POST, admin/groups, admin.groups.index
     */     
    public function an_administrator_create_a_group()
    { 
        $attributes = factory('App\Group')->raw();
        
        $this->post("admin/groups" , $attributes)
                ->assertStatus(302);
        
        $this->assertDatabaseHas('groups', $attributes);
    }
    
    /** @test 
     * Use Route: POST, admin/groups, admin.groups.index
     */     
    public function an_administrator_get_create_view_for_a_group()
    { 
        
        $this->get("admin/groups/create")
             ->assertStatus(200);
    }
    
    /** @test 
     * Use Route: DELETE, admin/groups/massDestroy admin.groups.massDestroy
     */  
    public function an_administrator_can_mass_delete_groups()
    {        
        
        $this->post("admin/groups" , $group1 = factory('App\Group')->raw());
        $ids[] = Group::where('title', $group1['title'])->first()->id;
       
        $this->post("admin/groups" , $group2 = factory('App\Group')->raw());
        $ids[] = Group::where('title', $group2['title'])->first()->id;
        
        
        $this->delete("/admin/groups/massDestroy" , $attributes = [
                    'ids' =>  $ids,
                ])->assertStatus(204);   
        
        foreach($ids AS $id){
            
            $this->assertDatabaseMissing('groups', [
                'id' => $id
            ]);  
        }
    }
    
    /** @test 
     * Use Route: DELETE, admin/groups/{group}, admin.groups.destroy
     */  
    public function an_administrator_delete_a_group()
    {
        
        $this->post("admin/groups" , $group = factory('App\Group')->raw());
        $id = Group::where('title', $group['title'])->first()->id;
        
        $this->followingRedirects()
                ->delete("admin/groups/". $id )
                ->assertStatus(200);
    }
    
    /** @test 
     * Use Route: GET|HEAD, admin/groups/{group}, admin.groups.show
     */
    public function an_administrator_see_details_of_an_group() 
    { 
        
        $this->post("admin/groups" , $group = factory('App\Group')->raw());
        $group = Group::where('title', $group['title'])->first();
        
        $this->get("admin/groups/{$group->id}")       
             ->assertStatus(200)
             ->assertViewHasAll(compact($group));
    }
    
    /** @test 
     * Use Route: PUT|PATCH, admin/groups/{group}, admin.groups.update
     */
    public function an_administrator_update_a_group()
    {
        $this->withoutExceptionHandling();
        $this->post("admin/groups" , $group = factory('App\Group')->raw());
        $group = Group::where('title', $group['title'])->first()->toArray();
               
        $this->assertDatabaseHas('groups', $group);
       
        $this->patch("admin/groups/". $group['id'] , $new_attributes = factory('App\Group')->raw());
        $group_edit = Group::where('title', $new_attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('groups', $group_edit);
    }
    
    /** @test 
     * Use Route: GET|HEAD, admin/groups/{group}/edit, admin.groups.edit
     */     
    public function an_administrator_get_edit_view_for_a_group()
    { 
        $this->post("admin/groups" , $group = factory('App\Group')->raw());
        $group = Group::where('title', $group['title'])->first();
        $this->withoutExceptionHandling();
        $this->get("admin/groups/{$group->id}/edit")
             ->assertStatus(200)
             ->assertSessionHasAll(compact($group));
    }
   
}
