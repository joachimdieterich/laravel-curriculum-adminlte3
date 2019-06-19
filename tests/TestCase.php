<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use DatabaseSeeder;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    public function setUp(): void
    {
        parent::setUp();
        (new DatabaseSeeder())->call(DatabaseSeeder::class);
    }
    
    protected function signIn($user = null)
    {
        $user = $user ?: factory('App\User')->create();
        $this->actingAs($user);

        return $user;
    }
    
     protected function signInAdmin($user = null)
    {
        $credentials = [
            'email' => 'admin@curriculumonline.de',
            'password' => 'password',
        ];
        $this->followingRedirects()->post('login', $credentials)->assertStatus(200);
        $this->actingAs(auth()->user());
       
        
        return auth()->user();
    }
    
}
