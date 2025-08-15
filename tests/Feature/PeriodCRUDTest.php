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
        $periods = Period::select('title', 'begin', 'end')->get();
        $list = $this->get('periods/list')
            ->assertStatus(200);
        $i = 0;
        foreach ($periods as $period) {
            if ($i === 49) {
                break;
            } //test max 50 user (default page limit on datatables
            $list->assertJsonFragment($period->toArray());
            $i++;
        }
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
        $this->followingRedirects()
            ->post('periods', $attributes = Period::factory()->raw())
            ->assertStatus(200);

        $this->assertDatabaseHas('periods', $attributes);
    }

    /** @test
     * Use Route: DELETE, periods/{period}, periods.destroy
     */
    public function an_administrator_delete_a_period()
    {
        $period = Period::factory()->create();
        $this->followingRedirects()
            ->delete('periods/'.$period->id)
            ->assertStatus(200);
    }

    /** @test
     * Use Route: GET|HEAD, periods/{period}, periods.show
     */
    public function an_administrator_see_details_of_an_period()
    {
        $period = Period::factory()->create();

        $this->get("periods/{$period->id}")
             ->assertStatus(200)
             ->assertSee($period->toArray());
    }

    /** @test
     * Use Route: PUT|PATCH, periods/{period}, periods.update
     */
    public function an_administrator_update_a_period()
    {
        $this->post('periods', $attributes = Period::factory()->raw());
        $period = Period::where('title', $attributes['title'])->first()->toArray();

        $this->assertDatabaseHas('periods', $period);

        $this->patch('periods/'.$period['id'], $new_attributes = Period::factory()->raw());

        $period_edit = Period::where('title', $new_attributes['title'])->first()->toArray();
        $this->assertDatabaseHas('periods', $period_edit);
    }

    /** @test
     * Use Route: GET|HEAD, periods/{period}/edit, periods.edit
     */
    public function an_administrator_get_edit_view_for_a_period()
    {
        $this->post('periods', $attributes = Period::factory()->raw());
        $period = Period::where('title', $attributes['title'])->first();

        $this->get("periods/{$period->id}/edit")
            ->assertStatus(200)
            ->assertSee([
                'title' => $period->title,
                'begin' => $period->begin,
                'end' => $period->end,
            ]);
    }
}
