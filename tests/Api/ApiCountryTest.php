<?php

namespace Tests\Api;

use App\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCountryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_client_can_not_get_countries()
    {
        $response = $this->get('/api/v1/countries')->assertStatus(302);
        $this->stringContains('login');
    }

    /** @test
     * Use Route: GET, /api/v1/countries
     */
    public function an_authenticated_client_can_get_all_countries()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/countries')
                ->assertStatus(200)
                ->assertJson(Country::all()->toArray());
    }

    /** @test
     * Use Route: GET, /api/v1/countries/{id}
     */
    public function an_authenticated_client_can_get_a_country()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/countries/DE')
                ->assertStatus(200)
                ->assertJson(Country::find('DE')->toArray());
    }
}
