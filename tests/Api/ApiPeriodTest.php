<?php

namespace Tests\Api;

use App\Period;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiPeriodTest extends TestCase
{
    use RefreshDatabase;

    /** @test
     * Use Route: GET, /api/v1/periods
     */
    public function an_authenticated_client_can_not_get_periods()
    {
        $this->get('/api/v1/periods')->assertStatus(302);
        $this->stringContains('login');
    }

    /** @test
     * Use Route: GET, /api/v1/periods
     */
    public function an_authenticated_client_can_get_all_periods()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/periods')
                ->assertStatus(200)
                ->assertJson(Period::all()->toArray());
    }

    /** @test
     * Use Route: GET, /api/v1/periods/{id}
     */
    public function an_authenticated_client_can_get_a_period()
    {
        $this->signInApiAdmin();

        $this->get('/api/v1/periods/1')
                ->assertStatus(200)
                ->assertJson(Period::find(1)->toArray());
    }

    /** @test
     * Use Route: POST, /api/v1/periods
     */
    public function an_authenticated_client_can_create_a_period()
    {
        $this->signInApiAdmin();

        $this->post('/api/v1/periods', $attributes = Period::factory()->raw());

        $this->assertDatabaseHas('periods', $attributes);
    }

    /** @test
     * Use Route: PUT, /api/v1/periods/{id}
     */
    public function an_authenticated_client_can_update_a_period()
    {
        $this->signInApiAdmin();

        $new_period = $this->post('/api/v1/periods', $attributes = Period::factory()->raw());

        $this->put("/api/v1/periods/{$new_period->getData()->id}", $changed_attribute = ['title' => 'New Title']);

        $changed_attribute = array_filter($changed_attribute);

        $this->get("/api/v1/periods/{$new_period->getData()->id}")
             ->assertStatus(200)
             ->assertJson($changed_attribute);
    }

    /** @test
     * Use Route: DELETE, /api/v1/periods/{id}
     */
    public function an_authenticated_client_can_delete_a_period()
    {
        $this->signInApiAdmin();

        $new_period = $this->post('/api/v1/periods', $attributes = Period::factory()->raw());

        $this->delete("/api/v1/periods/{$new_period->getData()->id}");

        $this->assertDatabaseMissing('periods', $attributes);
    }
}
