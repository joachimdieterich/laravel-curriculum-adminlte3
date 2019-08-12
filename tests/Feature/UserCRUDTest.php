<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Facades\Tests\Setup\UserFactory;

class UserCRUDTest extends TestCase
{
    use RefreshDatabase;
    
     public function setUp(): void
    {
        
        parent::setUp();
        $this->signInAdmin();
    }
    
    /** @test 
     * Use Route: GET|HEAD, users, users.index
     */
    public function an_administrator_see_users() 
    { 
        
        $this->get("users")       
             ->assertStatus(200);
             
        /* Use Datatables */
        $users = User::all();
        $this->get("users/list")
             ->assertStatus(200)
             ->assertViewHasAll(compact($users));
    }
    
    /** @test 
     * Use Route: POST, users, users.store
     */  
    public function an_administrator_create_an_user()
    {
        $this->withoutExceptionHandling();
        $this->followingRedirects()->post("users" , $attributes = factory('App\User')->raw())
                ->assertStatus(200);
        
        $this->assertDatabaseHas('users', [
            'username' => $attributes['username'],
            'firstname' => $attributes['firstname'],
            'lastname' => $attributes['lastname'],
            'email' => $attributes['email']
        ]);
    }
   
    /** @test 
     * Use Route: POST, roles, roles.index
     */     
    public function an_administrator_get_create_view_for_users()
    { 
        
        $this->get("users/create")
             ->assertStatus(200);
    }
     
    /** @test 
     * Use Route: DELETE, users/massDestroy users.massDestroy
     */  
    public function an_admin_can_mass_delete_users()
    {        
        
        $users = factory(User::class, 50)->create();
        $ids = $users->pluck('id')->toArray();
 
        $this->delete("/users/massDestroy" , $attributes = [
                    'ids' =>  $ids,
                ])->assertStatus(204);   
        
        foreach($ids AS $id){
            $this->assertSoftDeleted('users', [
                'id' => $id
            ]);  
        }
    }
    
    /** @test 
     * Use Route: DELETE, users/{user}, users.destroy
     */  
    public function an_administrator_delete_a_role()
    {
        $this->withoutExceptionHandling();
        $user = UserFactory::create();     
        
        $this->followingRedirects()
                ->delete("users/". $user->id )
                ->assertStatus(200);
    }
    
    /** @test 
     * Use Route: GET|HEAD, users/{user}, users.show
     */
    public function an_administrator_see_details_of_an_user() 
    { 
        
        $user = UserFactory::create();
        
        $this->get("users/{$user->id}")       
             ->assertStatus(200)
             ->assertViewHasAll(compact($user));
    }

    /** @test 
     * Use Route: PUT|PATCH, users/{user}, users.update
     */
    public function an_administrator_update_an_user()
    {
        $this->post("users" , $attributes = factory('App\User')->raw());
        $user = User::where('username', $attributes['username'])->first()->toArray();
      
        $this->assertDatabaseHas('users', $user);
        
        $this->patch("users/". $user['id'] , $new_attributes = factory('App\User')->raw());
        
        $user_edit = User::where('username', $new_attributes['username'])->first()->toArray();
        $this->assertDatabaseHas('users', $user_edit);
    }
    
    /** @test 
     * Use Route: GET|HEAD, users/{user}/edit, users.edit
     */     
    public function an_administrator_get_edit_view_for_users()
    { 
        $this->post("users" , $attributes = factory('App\User')->raw());
        $user = User::where('username', $attributes['username'])->first();
        
        $this->get("users/{$user->id}/edit")
             ->assertStatus(200)
             ->assertSessionHasAll(compact($user));
    }
}
