<?php

namespace Tests\Feature;

use App\Config;
use App\Logbook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentLogbookCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInRole('student');
    }

    /** @test
     * Use Route: GET|HEAD, logbooks, logbooks.index
     */
    public function a_student_see_logbooks()
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
    public function a_student_create_an_Logbook()
    {
        $this->followingRedirects()->post('logbooks', $attributes = Logbook::factory()->raw())
            ->assertStatus(200);

        $this->assertDatabaseHas('logbooks', $attributes);
    }

    /** @test
     * Use Route: GET, logbooks, logbooks.create
     */
    public function a_student_get_create_view_for_logbooks()
    {
        $this->get('logbooks/create')
            ->assertStatus(200);
    }

    /** @test
     * Use Route: GET, logbooks, logbooks.create
     */
    public function a_student_cannot_get_create_view_for_logbooks_if_limiter_is_reached()
    {
        $this->get('logbooks/create')
            ->assertStatus(200); //limit not reached

        $logbook = Logbook::factory()->create();

        Config::create([
            'key' => 'logbook_limiter',
            'value' => 1,
            'referenceable_type' => 'App\\Role',
            'referenceable_id' => 6,
            'data_type' => 'integer',
        ]); // define limit = 1

        $this->get('logbooks/create')
            ->assertStatus(402); // limit reached
    }

    /** @test
     * Use Route: DELETE, logbooks/{Logbook}, logbooks.destroy
     */
    public function a_student_cannot_delete_a_Logbook()
    {
        $logbook = Logbook::factory()->create();

        $this->followingRedirects()
            ->delete('logbooks/'.$logbook->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('logbooks', $logbook->toArray());
    }

    /** @test
     * Use Route: GET|HEAD, logbooks/{Logbook}, logbooks.show
     */
    public function a_student_see_details_of_a_Logbook()
    {
        $logbook = Logbook::factory()->create();

        //dd($logbook->id);
        $this->get("logbooks/{$logbook->id}")
            ->assertStatus(200)
            ->assertSee($logbook->toArray());
    }

    /** @test
     * Use Route: GET|HEAD, logbooks/{Logbook}, logbooks.show
     */
    public function a_student_cannot_see_details_of_a_foreign_Logbook()
    {
        $logbook = Logbook::factory()->create();

        $logbook->owner_id = 1;
        $logbook->save();
        $this->get("logbooks/{$logbook->id}")
            ->assertStatus(403);
    }

    /** @test
     * Use Route: PUT|PATCH, logbooks/{Logbook}, logbooks.update
     */
    public function a_student_update_a_Logbook()
    {
        $this->post('logbooks', $attributes = Logbook::factory()->raw());

        $this->assertDatabaseHas('logbooks', $attributes);

        $this->patch('logbooks/'.Logbook::where('title', '=', $attributes['title'])->first()->id, $new_attributes = Logbook::factory()->raw());

        $this->assertDatabaseHas('logbooks', $new_attributes);
    }

    /** @test
     * Use Route: PUT|PATCH, logbooks/{Logbook}, logbooks.update
     */
    public function a_student_cannot_update_a_foreign_Logbook()
    {
        $logbook = Logbook::factory()->create();

        $logbook->owner_id = 1;
        $logbook->save();

        $this->patch("logbooks/{$logbook->id}", Logbook::factory()->raw())
            ->assertStatus(403);
    }

    /** @test
     * Use Route: GET|HEAD, logbooks/{Logbook}/edit, logbooks.edit
     */
    public function a_student_get_edit_view_for_a_Logbook()
    {
        $logbook = Logbook::factory()->create();

        $this->get("logbooks/{$logbook->id}/edit")
            ->assertStatus(200)
            ->assertSee($logbook->toArray());
    }

    /** @test
     * Use Route: GET|HEAD, logbooks/{Logbook}/edit, logbooks.edit
     */
    public function a_student_cannot_get_edit_view_for_a_foreign_Logbook()
    {
        $logbook = Logbook::factory()->create();

        $logbook->owner_id = 1;
        $logbook->save();

        $this->get("logbooks/{$logbook->id}/edit")
            ->assertStatus(403);
    }
}
