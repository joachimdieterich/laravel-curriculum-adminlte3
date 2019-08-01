<?php

namespace Tests\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiStateTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    public function an_unauthificated_client_can_not_get_states() 
    {
        
        $this->get('/api/v1/states')->assertStatus(302);
        $this->contains('login');
    }

    /** @test 
     * Use Route: GET, /api/v1/states
     */
    public function an_authificated_client_can_get_all_states() 
    {

        $this->signInApiAdmin();

        $this->get('/api/v1/states')
                ->assertStatus(200);
    }

    /** @test 
     * Use Route: GET, /api/v1/states/{id}
     */
    public function an_authificated_client_can_get_a_state() 
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/states/11')
                ->assertStatus(200)
                ->assertJson([
                   "id"=> 11,
                    "lang_de"=> "Rheinland-Pfalz",
                    "country"=> "DE",
                    "code"=> "DE-RP"
        ]);
    }

}
