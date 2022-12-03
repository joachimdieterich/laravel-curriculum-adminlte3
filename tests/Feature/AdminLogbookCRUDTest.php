<?php

namespace Tests\Feature;

use App\Logbook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLogbookCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInRole('admin');
    }

    /** @test
     * Use Route: GET|HEAD, logbooks, logbooks.index
     */
    public function an_administrator_see_logbooks()
    {
        $this->get('logbooks')
            ->assertStatus(200);

        /* Use Datatables */
        $logbooks = Logbook::select('id', 'title')->get();
        $list = $this->get('logbooks/list')
            ->assertStatus(200);
        $i = 0;
        foreach ($logbooks as $logbook)
        {
            if ($i === 49) { break; } //test max 50 entries (default page limit on datatables
            $list->assertJsonFragment($logbook->toArray());
            $i++;
        }
    }

    /** @test
     * Use Route: POST, logbooks, logbooks.store
     */
    public function an_administrator_create_an_Logbook()
    {
        $this->followingRedirects()->post('logbooks', $attributes = Logbook::factory()->raw())
            ->assertStatus(200);

        $this->assertDatabaseHas('logbooks', $attributes);
    }

    /** @test
     * Use Route: POST, logbooks, logbooks.index
     */
    public function an_administrator_get_create_view_for_logbooks()
    {
        $this->get('logbooks/create')
            ->assertStatus(200);
    }

    /** @test
     * Use Route: DELETE, logbooks/{Logbook}, logbooks.destroy
     */
    public function an_administrator_delete_a_Logbook()
    {
        $logbook = Logbook::factory()->create();

        $this->followingRedirects()
            ->delete('logbooks/'.$logbook->id)
            ->assertStatus(200);

        $this->assertDatabaseMissing('logbooks', $logbook->toArray());
    }

    /** @test
     * Use Route: GET|HEAD, logbooks/{Logbook}, logbooks.show
     */
    public function an_administrator_see_details_of_a_Logbook()
    {
        $logbook = Logbook::factory()->create();

        $this->get("logbooks/{$logbook->id}")
            ->assertStatus(200)
            ->assertsee($logbook->toArray());
    }

    /** @test
     * Use Route: PUT|PATCH, logbooks/{Logbook}, logbooks.update
     */
    public function an_administrator_update_a_Logbook()
    {
        $this->post('logbooks', $attributes = Logbook::factory()->raw());

        $this->assertDatabaseHas('logbooks', $attributes);

        $this->patch('logbooks/'.Logbook::where('title', '=', $attributes['title'])->first()->id, $new_attributes = Logbook::factory()->raw());

        $this->assertDatabaseHas('logbooks', $new_attributes);
    }

    /** @test
     * Use Route: GET|HEAD, logbooks/{Logbook}/edit, logbooks.edit
     */
    public function an_administrator_get_edit_view_for_a_Logbook()
    {
        $logbook = Logbook::factory()->create();

        $this->get("logbooks/{$logbook->id}/edit")
            ->assertStatus(200)
            ->assertSee($logbook->toArray());
    }
}
