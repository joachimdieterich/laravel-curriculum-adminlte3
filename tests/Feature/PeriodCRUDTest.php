<?php

namespace Tests\Feature;

use App\Period;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeriodCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInAdmin();
    }

    /** @test
     * Use Route: GET|HEAD, periods, periods.index
     */
    public function an_administrator_see_periods()
    {
        $this->get('periods')
             ->assertStatus(200);

        /* Use Datatables */
        $periods = Period::first();
        $this->get('periods/list')
             ->assertStatus(200)
             ->assertViewHasAll(compact($periods));
    }

    /** @test
     * Use Route: POST, periods, periods.index
     */
    public function an_administrator_get_create_view_for_a_period()
    {
        $this->get('periods/create')
             ->assertStatus(200);
    }

    /** @test
     * Use Route: POST, periods, periods.index
     */
    public function an_administrator_create_a_period()
    {
        $attributes = factory('App\Period')->raw();

        $this->post('periods', $attributes)
                ->assertStatus(302);

        $group = Period::where('title', $attributes['title'])->first();
        $this->assertDatabaseHas('periods', $group->toArray());
    }

    /** @test
     * Use Route: DELETE, periods/{period}, periods.destroy
     */
    public function an_administrator_delete_a_period()
    {
        $this->post('periods', $period = factory('App\Period')->raw());
        $id = Period::where('title', $period['title'])->first()->id;

        $this->followingRedirects()
                ->delete('periods/'.$id)
                ->assertStatus(200);
    }

    /** @test
     * Use Route: GET|HEAD, periods/{period}, periods.show
     */
    public function an_administrator_see_details_of_an_period()
    {
        $this->post('periods', $attributes = factory('App\Period')->raw());
        $period = Period::where('title', $attributes['title'])->get()->first();

        $this->get(route('periods.show', $period->id))
             ->assertStatus(200)
             ->assertViewHasAll(compact($period));
    }

    /** @test
     * Use Route: PUT|PATCH, periods/{period}, periods.update
     */
    public function an_administrator_update_a_period()
    {
        $this->post('periods', $attributes = factory('App\Period')->raw());
        $period = Period::where('title', $attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('periods', $period);

        $this->patch('periods/'.$period['id'], $new_attributes = factory('App\Period')->raw());
        $period_edit = Period::where('title', $new_attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('periods', $period_edit);
    }

    /** @test
     * Use Route: GET|HEAD, periods/{period}/edit, periods.edit
     */
    public function an_administrator_get_edit_view_for_a_period()
    {
        $this->post('periods', $period = factory('App\Period')->raw());
        $period = Period::where('title', $period['title'])->first();

        $this->get("periods/{$period->id}/edit")
             ->assertStatus(200)
             ->assertSessionHasAll(compact($period));
    }
}
