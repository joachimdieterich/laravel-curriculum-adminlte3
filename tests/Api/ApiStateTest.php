<?php

namespace Tests\Api;

use App\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiStateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_client_can_not_get_states()
    {
        $this->get('/api/v1/states')
             ->assertStatus(302);
        $this->stringContains('login');
    }

    /** @test
     * Use Route: GET, /api/v1/states
     */
    public function an_authenticated_client_can_get_all_states()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/states')
                ->assertStatus(200)
                ->assertJson(State::all()->toArray());
    }

    /** @test
     * Use Route: GET, /api/v1/states/{id}
     */
    public function an_authenticated_client_can_get_a_state()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/states/DE-RP')
                ->assertStatus(200)
                ->assertJson(State::find('DE-RP')->toArray());
    }
}
