<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class ManagePlatformTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function an_admin_can_see_the_impressum()
    {
        $this->withoutExceptionHandling();
        $user = $this->signInAdmin();
        
        $this->get('impressum/')->assertStatus(200);
        
       
    }
    
}
