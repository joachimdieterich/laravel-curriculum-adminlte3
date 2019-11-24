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
        $this->get('/api/v1/users')->assertStatus(302);
        $this->contains('login');
    }
    
     /** @test 
     * Use Route: GET, /api/v1/users
     */   
    public function an_authificated_client_can_get_all_users()
    {
        $this->signInApiAdmin();
        
        $this->get('/api/v1/users')
             ->assertStatus(200)
             ->assertJson(User::all()->toArray()); 
    }
    
    /** @test 
     * Use Route: GET, /api/v1/users/{id}
     */     
    public function an_authificated_client_can_get_an_user()
    { 
        $this->signInApiAdmin();

        $this->get('/api/v1/users/1') 
             ->assertStatus(200)
             ->assertJson(User::find(1)->toArray()); 
    }
    
    /** @test 
     * Use Route: POST, /api/v1/users
     */     
    public function an_authificated_client_can_create_an_user()
    { 
        $this->signInApiAdmin();
        
        $this->post("/api/v1/users" , $attributes = [
            'username' => 'username',
            'common_name' => "cn_username",
            'firstname' => "firstname",
            'lastname' => "lastname",
            'email' => "username@curriclumonline.de",
            'email_verified_at' => date("Y-m-d H:i:s"),
            'password' => 'password', 
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
        $this->withoutExceptionHandling();
        $new_user = $this->post("/api/v1/users" ,  $attributes = [
            'username' => 'username',
            'common_name' => "cn_username",
            'firstname' => "firstname",
            'lastname' => "lastname",
            'email' => "username@curriclumonline.de",
            'email_verified_at' => date("Y-m-d H:i:s"),
            'password' => 'password', // password
        ]); 
        
        $this->put("/api/v1/users/{$new_user->getData()->id}" , 
                        ['firstname' => 'newfirstname',
                         'lastname' => 'newlastname']
                  ); 
        
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
        
        $new_user = $this->post("/api/v1/users" ,  $attributes = [
            'username' => 'username',
            'common_name' => "cn_username",
            'firstname' => "firstname",
            'lastname' => "lastname",
            'email' => "username@curriclumonline.de",
            'email_verified_at' => date("Y-m-d H:i:s"),
            'password' => 'password', 
        ]); 
        
        $this->delete("/api/v1/users/{$new_user->getData()->id}"); 
        
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
