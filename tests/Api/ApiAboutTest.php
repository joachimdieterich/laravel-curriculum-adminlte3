<?php

namespace Tests\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiAboutTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function any_user_can_call_about()
    {
        $this->get('/api/v1/about')->assertStatus(200);
        $this->contains('Curriculum API V1');
    }
    
}
