<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    
    /** @test */
    public function has_a_group() {
        
        $user = User::findOrFail(1); 
        
        $this->assertInstanceOf('App\Group', $user->groups()->first());
    }
    
}
