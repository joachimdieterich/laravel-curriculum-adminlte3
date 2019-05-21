<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Organization;
use App\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Facades\Tests\Setup\OrganizationFactory;
use Facades\Tests\Setup\RoleFactory;
use Facades\Tests\Setup\UserFactory;
use Illuminate\Database\QueryException;


class ManageUserTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function an_administrator_create_a_user()
    {
        $this->signInAdmin();
        
        $this->followingRedirects()->post("admin/users" , $attributes = factory('App\User')->raw())
                ->assertStatus(200);
        
        $this->assertDatabaseHas('users', [
            'username' => $attributes['username'],
            'firstname' => $attributes['firstname'],
            'lastname' => $attributes['lastname'],
            'email' => $attributes['email']
        ]);
    }
    
    
    
    /** @test */
    public function an_administrator_can_enrol_an_user_to_an_institution_with_a_role()
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
        $user = $this->signIn();

        $role_admin = RoleFactory::create();
        
        $organization1 = OrganizationFactory::create();
        
        $user->enrol($user->id, $organization1->id, $role_admin->id); //returns OrganizationRoleUser
        $this->assertDatabaseHas('organization_role_users', [
            'user_id' => $user->id, 
            'organization_id' => $organization1->id, 
            'role_id' => $role_admin->id
            ]);
        $this->assertEquals($role_admin->title, $user->roles->first()->title);
    }
    
    /** @test */
    public function a_user_can_be_expelled_from_an_institution_by_the_admin()
    {        
        $user = $this->signInAdmin();
        
        $role_admin = RoleFactory::create();
        
        $organization1 = OrganizationFactory::create();
        
        $user_expel = factory(User::class)->create();
        
        $user->enrol($user_expel->id, $organization1->id, $role_admin->id); //returns OrganizationRoleUser
        
        $this->assertDatabaseHas('organization_role_users', [
            'user_id' => $user_expel->id, 
            'organization_id' => $organization1->id, 
            'role_id' => $role_admin->id
            ]);
        $this->assertEquals($role_admin->title, $user_expel->roles->first()->title);
        
        $this->followingRedirects()->post("/admin/users/". $user->id ."/organization/". $organization1->id ."/expel")
                ->assertStatus(200);   
    }
    
    /** @test */
//    public function an_admin_can_mass_update_user_passwords()
//    {        
//        $user = $this->signInAdmin();
//        
//        $this->factory(App\User::class, 50)->create();
//        
//        $this->followingRedirects()->post("/admin/users/massUpdate" , $attributes = [
//                    'ids' => [
//                        
//                    ],
//                    'organizations' => [
//                        ($org2 = OrganizationFactory::create())->id
//                    ], 
//                ])
//                ->assertStatus(200);
//        
//        $user->enrol($user_expel->id, $organization1->id, $role_admin->id); //returns OrganizationRoleUser
//        
//        $this->assertDatabaseHas('organization_role_users', [
//            'user_id' => $user_expel->id, 
//            'organization_id' => $organization1->id, 
//            'role_id' => $role_admin->id
//            ]);
//        $this->assertEquals($role_admin->title, $user_expel->roles->first()->title);
//        
//        $this->followingRedirects()->post("/admin/users/". $user->id ."/organization/". $organization1->id ."/expel")
//                ->assertStatus(200);   
//    }
}
