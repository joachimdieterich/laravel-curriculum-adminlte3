<?php

namespace Tests\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiPeriodTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    public function an_unauthificated_client_can_not_get_periods() 
    {
        
        $this->get('/api/v1/periods')->assertStatus(302);
        $this->contains('login');
    }

    /** @test 
     * Use Route: GET, /api/v1/periods
     */
    public function an_authificated_client_can_get_all_periods() 
    {

        $this->signInApiAdmin();

        $this->get('/api/v1/periods')
                ->assertStatus(200);
    }

    /** @test 
     * Use Route: GET, /api/v1/periods/{id}
     */
    public function an_authificated_client_can_get_a_period() 
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/periods/1')
                ->assertStatus(200)
                ->assertJson([
                    "id"=> 1,
                    "title"=> "Test",
                    "begin"=> "2019-04-15 19:14:42",
                    "end"=> "2023-04-15 19:14:42",
                    "organization_id"=> 1,
                    "owner_id"=> 1,
                    "created_at"=> null,
                    "updated_at"=> null
        ]);
    }

}
