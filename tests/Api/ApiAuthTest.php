<?php

namespace Tests\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_user_and_check_if_authenticated()
    {
        $this->post('/api/v1/login',
            [
                'email' => 'admin',
                'password' => 'password',
            ])->assertStatus(302);

        $this->signInApiAdmin();
        $user = $this->get('/api/v1/user');

        $this->assertEquals($user->baseResponse->original->id, 1);
    }
}
