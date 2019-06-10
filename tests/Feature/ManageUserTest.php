<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Facades\Tests\Setup\OrganizationFactory;
use Facades\Tests\Setup\RoleFactory;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class ManageUserTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function an_administrator_can_enrol_an_user_to_an_organization_with_a_role()
    {
        //$this->withoutExceptionHandling();
        $user = $this->signInAdmin();
        
        $role_admin_id   = 1;
        $role_creator_id = 2;
        
        ///admin/users/{user}/organization/enrol
        $this->followingRedirects()->post("/admin/users/". $user->id ."/organization/enrol" , $attributes = [
                    'role_id' => $role_admin_id,
                    'organizations' => [
                        ($org1 = OrganizationFactory::create())->id
                    ], 
                ])
                ->assertStatus(200);
        $this->assertDatabaseHas('organization_role_users', [
            'user_id' => auth()->user()->id, 
            'organization_id' => $org1->id, 
            'role_id' => $role_admin_id
            ]);
        
        $this->followingRedirects()->post("/admin/users/". $user->id ."/organization/enrol" , $attributes = [
                    'role_id' => $role_creator_id,
                    'organizations' => [
                        ($org2 = OrganizationFactory::create())->id
                    ], 
                ])
                ->assertStatus(200);
        $this->assertDatabaseHas('organization_role_users', [
            'user_id' => auth()->user()->id,
            'organization_id' => $org2->id, 
            'role_id' => $role_creator_id
            ]);
        
        // Check for Integrity constraint violation
        try { 
            auth()->user()->enrol(auth()->user()->id, $org2->id, $role_creator_id); //->should fail unique key error//Your code
        } catch(QueryException $e){ 
            $this->assertStringContainsString('Integrity constraint violation', $e->getMessage());
        }
        
    }
    
    /** @test */
    public function every_user_has_a_role()
    {        
        
        $user = $this->signInAdmin();        
        $this->post("admin/roles" , $role = factory('App\Role')->raw());
        $role_id = Role::where('title', $role['title'])->first()->id;
        
        $organization = OrganizationFactory::create();
        
        $user_to_enrol = factory('App\User')->create();
        $user->enrol('organization', $user_to_enrol->id, $organization->id, $role_id); //returns OrganizationRoleUser
        $this->assertDatabaseHas('organization_role_users', [
            'user_id' => $user_to_enrol->id, 
            'organization_id' => $organization->id, 
            'role_id' => $role_id
            ]);
        $this->assertEquals($role['title'], $user_to_enrol->roles->first()->title);
    }
    
    /** @test */
    public function a_user_can_be_expelled_from_an_organization_by_the_admin()
    {        
        $user = $this->signInAdmin();
        
        $this->post("admin/roles" , $role = factory('App\Role')->raw());
        $role_id = Role::where('title', $role['title'])->first()->id;
        
        $organization = OrganizationFactory::create();
        
        $user_expel = factory(User::class)->create();
        
        $user->enrol('organization', $user_expel->id, $organization->id, $role_id); //returns OrganizationRoleUser
        
        $this->assertDatabaseHas('organization_role_users', [
            'user_id' => $user_expel->id, 
            'organization_id' => $organization->id, 
            'role_id' => $role_id
            ]);
        $this->assertEquals($role['title'], $user_expel->roles->first()->title);
        
        $this->followingRedirects()->post("/admin/users/". $user->id ."/organization/". $organization->id ."/expel")
                ->assertStatus(200);   
    }
    
    /** @test */
    public function an_admin_can_mass_update_user_passwords()
    {        
        $user = $this->signInAdmin();
        //$this->withoutExceptionHandling();
        $users = factory(User::class, 50)->create();
        $ids = $users->pluck('id')->toArray();
        
        $new_password = Hash::make('new_password');
        $this->patch("/admin/users/massUpdate" , $attributes = [
                    'ids' =>  $ids,
                    'password' => $new_password
                ]) ->assertStatus(204);   
        
        foreach($ids AS $id){
            $this->assertTrue(\Hash::check($new_password, User::find($id)->password));     
        }
    }
    
    /** @test */
    public function an_admin_can_mass_update_user_status()
    {        
        $user = $this->signInAdmin();
        //$this->withoutExceptionHandling();
        $users = factory(User::class, 50)->create();
        $ids = $users->pluck('id')->toArray();
        
        $this->patch("/admin/users/massUpdate" , $attributes = [
                    'ids' =>  $ids,
                    'status_id' => 2
                ])->assertStatus(204);   
        
        foreach($ids AS $id){
            $this->assertEquals(2, User::find($id)->status_id);     
        }
    }
    
}
