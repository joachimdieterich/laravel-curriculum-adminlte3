<?php

namespace Tests\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Facades\Tests\Setup\OrganizationFactory;
use Facades\Tests\Setup\UserFactory;
use App\User;

class ApiUserTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function an_unauthificated_client_can_not_get_users()
    {
          
        $response = $this->get('/api/v1/users')->assertStatus(302);
        $this->contains('login');
    }
    
     /** @test 
     * Use Route: GET, /api/v1/users
     */   
    public function an_authificated_client_can_get_all_users()
    {
        
        $this->signInApiAdmin();
        
        $user = [[
            'id'             => 1,
            'username'       => 'Admin',
            'common_name'    => 'cn_Admin',
            'firstname'      => 'Global',
            'lastname'       => 'Admin',
            'email'          => 'admin@curriculumonline.de',
            'created_at'     => '2019-04-15 19:13:32',
            'updated_at'     => '2019-04-15 19:13:32',
            'deleted_at'     => null,
            'current_organization_id'     => 1,
            ],
            [
            'id'             => 2,
            'username'       => 'Student',
            'common_name'    => 'cn_Student',
            'firstname'      => 'Student',
            'lastname'       => 'Role',
            'email'          => 'student@curriculumonline.de',
            'created_at'     => '2019-04-15 19:13:32',
            'updated_at'     => '2019-04-15 19:13:32',
            'deleted_at'     => null,
            'current_organization_id'     => 1,
            ]];
        
        $this->get('/api/v1/users')
             ->assertStatus(200)
             ->assertJson($user); 
    }
    
    /** @test 
     * Use Route: GET, /api/v1/users/{id}
     */     
    public function an_authificated_client_can_get_an_user()
    { 
        $this->signInApiAdmin();
        
        $attributes = ["id"=> 1,
			"common_name"=> "cn_Admin",
			"username"=> "Admin",
			"firstname"=> "Global",
			"lastname"=> "Admin",
			"email"=> "admin@curriculumonline.de",
			"email_verified_at"=> null,
			"status_id"=> 2,
			"created_at"=> "2019-04-15 19:13:32",
			"updated_at"=> "2019-04-15 19:13:32",
			"deleted_at"=> null,
			"current_organization_id"=> 1,
			"current_period_id"=> null,
			"medium_id"=> null
                      ]; 

        $this->get('/api/v1/users/1') 
             ->assertStatus(200)
             ->assertJson($attributes); 
    }
    
    /** @test 
     * Use Route: POST, /api/v1/users
     */     
    public function an_authificated_client_can_create_an_user()
    { 
        $this->signInApiAdmin();
        //$this->withoutExceptionHandling();
        $this->post("/api/v1/users" , $attributes = [
            'username' => 'username',
            'common_name' => "cn_username",
            'firstname' => "firstname",
            'lastname' => "lastname",
            'email' => "username@curriclumonline.de",
            'email_verified_at' => date("Y-m-d H:i:s"),
            'password' => 'password', // password
        ]);

       
        $this->assertDatabaseHas('users',  [
            'username' => 'username',
            'common_name' => "cn_username",
            'firstname' => "firstname",
            'lastname' => "lastname",
            'email' => "username@curriclumonline.de",
            'email_verified_at' => date("Y-m-d H:i:s"),
        ]);
    } 
    
    /** @test 
     * Use Route: PUT, /api/v1/users/{id}
     */     
    public function an_authificated_client_can_update_an_user()
    { 
        $this->signInApiAdmin();
        //$this->withoutExceptionHandling();
        $this->post("/api/v1/users" ,  $attributes = [
            'username' => 'username',
            'common_name' => "cn_username",
            'firstname' => "firstname",
            'lastname' => "lastname",
            'email' => "username@curriclumonline.de",
            'email_verified_at' => date("Y-m-d H:i:s"),
            'password' => 'password', // password
        ]); //create new user with ID 3, ID 1+2 exists seeded
        
        $this->put("/api/v1/users/3" , ['firstname' => 'newfirstname',
                                                             'lastname' => 'newlastname']); 
        
        $this->assertDatabaseHas('users',  [
            'username' => 'username',
            'common_name' => "cn_username",
            'firstname' => 'newfirstname',
            'lastname' => 'newlastname',
            'email' => "username@curriclumonline.de",
            'email_verified_at' => date("Y-m-d H:i:s"),
        ]);
    }
    
    /** @test 
     * Use Route: DELETE, /api/v1/users/{id}
     */     
    public function an_authificated_client_can_delete_an_user()
    { 
        $this->signInApiAdmin();
        
        $this->post("/api/v1/users" ,  $attributes = [
            'username' => 'username',
            'common_name' => "cn_username",
            'firstname' => "firstname",
            'lastname' => "lastname",
            'email' => "username@curriclumonline.de",
            'email_verified_at' => date("Y-m-d H:i:s"),
            'password' => 'password', // password
        ]); //create new user with ID 3, ID 1+2 exists seeded
        
        $this->delete("/api/v1/users/3"); 
        
        $this->assertDatabaseMissing('users', [
            'username' => 'username',
            'common_name' => "cn_username",
            'firstname' => 'newfirstname',
            'lastname' => 'newlastname',
            'email' => "username@curriclumonline.de",
            'email_verified_at' => date("Y-m-d H:i:s"),
        ]);
    }
    
    
}
