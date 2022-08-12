<?php

namespace Tests\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAboutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function any_user_can_call_about()
    {
        $this->get('/api/v1/about')->assertStatus(200);
        $this->stringContains('Curriculum API V1');
    }
}
