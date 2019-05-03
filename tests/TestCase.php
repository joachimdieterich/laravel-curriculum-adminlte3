<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use DatabaseSeeder;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    protected function signIn($user = null)
    {
        //$this->withoutExceptionHandling();
        $this->seeder();
        $user = $user ?: factory('App\User')->create();
        $this->actingAs($user);

        return $user;
    }
    
     protected function signInAdmin($user = null)
    {
        $this->seeder();
        //$this->withoutExceptionHandling();
        $credentials = [
            'email' => 'admin@curriculumonline.de',
            'password' => 'password',
        ];
        $this->followingRedirects()->post('login', $credentials)->assertStatus(200);
        $this->actingAs(auth()->user());
        
        
        return auth()->user();
    }
    
    public function seeder() {
        (new DatabaseSeeder())->call(DatabaseSeeder::class);
    }
}
